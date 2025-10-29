<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // tùy môi trường dev có thể tắt
    }

    /**
     * Hiển thị form gán (tùy chọn)
     */
    public function showAssignForm($svId)
    {
        $sv = SinhVien::findOrFail($svId);
        $phongs = Phong::where('trang_thai', '!=', 'Bảo trì')->get();
        return view('assignment.assign', compact('sv', 'phongs'));
    }

    /**
     * Thực hiện gán sinh viên vào phòng (transaction + lockForUpdate)
     */
    public function assign(Request $request, $svId)
    {
        $request->validate(['phong_id' => 'required|exists:phong,id']);

        return DB::transaction(function () use ($request, $svId) {
            $sv = SinhVien::lockForUpdate()->findOrFail($svId);
            $phong = Phong::lockForUpdate()->findOrFail($request->phong_id);

            // Kiểm tra hồ sơ đã duyệt (dựa trên cột trang_thai_ho_so)
            if (isset($sv->trang_thai_ho_so) && $sv->trang_thai_ho_so !== 'Đã duyệt') {
                return redirect()->back()->with('error', 'Hồ sơ sinh viên chưa được duyệt, không thể gán.');
            }

            // Nếu phòng bảo trì
            if ($phong->trang_thai === 'Bảo trì') {
                return redirect()->back()->with('error', 'Phòng đang bảo trì, không thể gán.');
            }

            // Kiểm tra giới tính theo khu (nếu có) hoặc theo phòng
            $requiredGender = $phong->khu && $phong->khu->gioi_tinh ? $phong->khu->gioi_tinh : $phong->gioi_tinh;
            if (!empty($requiredGender) && $requiredGender !== 'Cả hai') {
                if (trim(mb_strtolower($requiredGender)) !== trim(mb_strtolower($sv->gioi_tinh))) {
                    return redirect()->back()->with('error', 'Giới tính sinh viên không phù hợp với quy định khu/phòng.');
                }
            }

            // Kiểm tra sức chứa: chỉ đếm sinh viên đã duyệt
            $count = $phong->sinhviens()->where('trang_thai_ho_so', 'Đã duyệt')->count();
            if ($count >= (int)$phong->suc_chua) {
                return redirect()->back()->with('error', 'Phòng đã đầy.');
            }

            // Nếu sv đã ở phòng khác, tách ra trước
            if ($sv->phong_id && $sv->phong_id != $phong->id) {
                $old = Phong::lockForUpdate()->find($sv->phong_id);
                if ($old) {
                    // gỡ sv khỏi phòng cũ (chỉ gán null)
                    $sv->phong_id = null;
                    $sv->save();
                    $old->updateStatusBasedOnCapacity();
                }
            }

            // gán sv vào phòng
            $sv->phong_id = $phong->id;
            $sv->save();

            // cập nhật trạng thái phòng
            $phong->updateStatusBasedOnCapacity();

            return redirect()->route('phong.index')->with('status', 'Gán sinh viên vào phòng thành công');
        });
    }
}
