<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDonBaoTri;

class HoaDonBaoTriController extends Controller
{
    // Hiển thị danh sách hóa đơn
    public function index()
    {
        $hoaDons = HoaDonBaoTri::orderByRaw("
            CASE 
                WHEN trang_thai_thanh_toan = 'Chưa thanh toán' THEN 0
                ELSE 1
            END
        ")
            ->latest()
            ->paginate(10);

        return view('lichbaotri.hoadonbaotri', compact('hoaDons'));
    }

    // Cập nhật hóa đơn
    public function update(Request $request, $id)
    {
        // ✅ Validate chung
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|string',
            'ghi_chu' => 'nullable|string',
            'anh_minh_chung' => 'nullable|image|max:2048',
        ]);

        // ✅ Nếu là chuyển khoản → bắt buộc có ảnh
        if ($request->phuong_thuc_thanh_toan === 'Chuyển khoản') {
            $request->validate([
                'anh_minh_chung' => 'required|image|max:2048',
            ]);
        }

        $hoaDon = HoaDonBaoTri::findOrFail($id);

        // ✅ Upload ảnh minh chứng (nếu có)
        if ($request->hasFile('anh_minh_chung')) {
            $path = $request->file('anh_minh_chung')
                ->store('hoa_don_bao_tri', 'public');

            $hoaDon->anh_minh_chung = $path;
        }

        // ✅ Cập nhật hóa đơn
        $hoaDon->trang_thai_thanh_toan = 'Đã thanh toán';
        $hoaDon->phuong_thuc_thanh_toan = $request->phuong_thuc_thanh_toan;
        $hoaDon->ghi_chu = $request->ghi_chu; // ❗ sửa đúng tên field
        $hoaDon->save();

        // ✅ Cập nhật trạng thái lịch bảo trì
        $lich = $hoaDon->lichBaoTri;
        if ($lich) {
            $lich->trang_thai = 'Hoàn thành';
            $lich->ngay_hoan_thanh = now();
            $lich->save();
        }

        return redirect()
            ->route('hoadonbaotri.index')
            ->with('success', 'Cập nhật hóa đơn thành công!');
    }
}
