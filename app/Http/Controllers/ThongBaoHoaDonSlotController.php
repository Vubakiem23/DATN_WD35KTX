<?php

namespace App\Http\Controllers;

use App\Models\HoaDonSlotPayment;

class ThongBaoHoaDonSlotController extends Controller
{
    public function index()
    {
        // Lấy danh sách sinh viên đã thanh toán kèm phòng
        $daThanhToan = HoaDonSlotPayment::with('phong')
            ->where('da_thanh_toan', 1)
            ->orderBy('ngay_thanh_toan', 'desc')
            ->get();

        // Lấy danh sách sinh viên chưa thanh toán kèm phòng
        $chuaThanhToan = HoaDonSlotPayment::with('phong')
            ->where('da_thanh_toan', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.thongbao_hoadonslot', compact('daThanhToan', 'chuaThanhToan'));
    }
}
