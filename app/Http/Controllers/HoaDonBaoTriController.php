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
")->latest()->paginate(10);
        return view('lichbaotri.hoadonbaotri', compact('hoaDons'));
    }

    // Cập nhật hóa đơn
    public function update(Request $request, $id)
    {
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|string',
            'ghi_chu' => 'nullable|string',
        ]);

        $hoaDon = HoaDonBaoTri::findOrFail($id);

        // Cập nhật hóa đơn
        $hoaDon->trang_thai_thanh_toan = 'Đã thanh toán';
        $hoaDon->phuong_thuc_thanh_toan = $request->phuong_thuc_thanh_toan;
        $hoaDon->ghi_chu = $request->ghi_chu_thanh_toan;
        $hoaDon->save();

        // ✅ Cập nhật trạng thái của lịch bảo trì về "Hoàn thành"
        $lich = $hoaDon->lichBaoTri;
        if ($lich) {
            $lich->trang_thai = 'Hoàn thành';
            $lich->ngay_hoan_thanh = now(); // nếu bạn muốn lưu ngày hoàn thành
            $lich->save();
        }

        return redirect()->route('hoadonbaotri.index')->with('success', 'Cập nhật hóa đơn thành công!');
    }
}
