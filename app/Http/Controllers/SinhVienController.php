<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SinhVienController extends Controller
{
    // Danh sách sinh viên (hiển thị + tìm kiếm)
    public function index(Request $request)
    {
        // giữ tương thích ?search= cũ
        $q          = $request->input('q', $request->input('search'));
        $gender     = $request->input('gender');         // Nam/Nữ/Khác
        $status     = $request->input('status');         // Đã duyệt/Chờ duyệt
        $roomId     = $request->input('room_id');        // phong_id
        $khu        = $request->input('khu');            // khu ở bảng phong
        $classLike  = $request->input('class_id');       // map vào 'lop'
        $majorLike  = $request->input('major_id');       // map vào 'nganh'
        $intakeYear = $request->input('intake_year');    // map vào 'khoa_hoc'

        $sinhviens = SinhVien::query()
            ->with('phong')
            ->search($q)
            ->gender($gender)
            ->hoSoStatus($status)
            ->inRoom($roomId)
            ->inKhu($khu)
            ->classLike($classLike)
            ->majorLike($majorLike)
            ->intakeYear($intakeYear)
            ->orderBy('id', 'desc')
            ->paginate(6)
            ->appends($request->query());

        // dữ liệu cho dropdown
        $phongs = \App\Models\Phong::select('id', 'ten_phong')->orderBy('ten_phong')->get();
        $dsKhu  = \App\Models\Phong::query()->select('khu')->distinct()->orderBy('khu')->pluck('khu');

        return view('sinhvien.index', [
            'sinhviens' => $sinhviens,
            'keyword'   => $q,
            'phongs'    => $phongs,
            'dsKhu'     => $dsKhu,
        ]);
    }


    /* Show modal */
    public function show($id)
    {
        $sinhvien = SinhVien::with('phong')->find($id);
        if (!$sinhvien) {
            return redirect()->route('sinhvien.index');
        }

        $html = view('sinhvien.show_modal', compact('sinhvien'))->render();

        return response()->json(['data' => $html]);
    }

    // Form thêm mới
    public function create()
    {
        $phongs = Phong::all();
        return view('sinhvien.create', compact('phongs'));
    }

    // Lưu sinh viên mới
    public function store(Request $request)
    {
        $request->validate([
            'ma_sinh_vien' => 'required|unique:sinh_vien',
            'ho_ten' => 'required',
            'email' => 'required|email|unique:sinh_vien',
        ]);

        $data = $request->all();
        $data['trang_thai_ho_so'] = $request->input('trang_thai_ho_so', 'Chờ duyệt');

        SinhVien::create($data);

        return redirect()->route('sinhvien.index')->with('success', 'Thêm sinh viên thành công!');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $sinhvien = SinhVien::findOrFail($id);
        $phongs = Phong::all();

        return view('sinhvien.edit', compact('sinhvien', 'phongs'));
    }

    // Cập nhật thông tin
    public function update(Request $request, $id)
    {
        $sinhvien = SinhVien::findOrFail($id);

        $request->validate([
            'email' => "required|email|unique:sinh_vien,email,$id",
        ]);

        $sinhvien->update($request->all());

        return redirect()->route('sinhvien.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    // 🧹 Xóa sinh viên + dữ liệu liên quan
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Xóa các bản ghi đăng ký KTX liên quan
            DB::table('dang_ky_k_t_x')->where('sinh_vien_id', $id)->delete();

            // Xóa sinh viên
            $sinhvien = SinhVien::findOrFail($id);
            $sinhvien->delete();

            DB::commit();

            return redirect()->route('sinhvien.index')->with('success', 'Đã xóa sinh viên và dữ liệu liên quan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sinhvien.index')
                ->with('error', 'Không thể xóa sinh viên vì đang liên kết với dữ liệu khác.');
        }
    }

    // Duyệt hồ sơ (thay đổi trạng thái)
    public function approve($id)
    {
        $sv = SinhVien::findOrFail($id);
        $sv->trang_thai_ho_so = 'Đã duyệt';
        $sv->save();

        return back()->with('success', 'Đã duyệt hồ sơ sinh viên.');
    }
}
