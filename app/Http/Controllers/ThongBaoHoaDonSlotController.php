<?php

namespace App\Http\Controllers;

use App\Models\HoaDonSlotPayment;

class ThongBaoHoaDonSlotController extends Controller
{
    public function index()
    {
        $daThanhToan = HoaDonSlotPayment::with(['phong', 'sinhVien'])
    ->where('da_thanh_toan', 1)
    ->orderBy('ngay_thanh_toan', 'desc')
    ->get();

$chuaThanhToan = HoaDonSlotPayment::with(['phong', 'sinhVien'])
    ->where('da_thanh_toan', 0)
    ->orderBy('created_at', 'desc')
    ->get();


        return view('client.thongbao_hoadonslot', compact('daThanhToan', 'chuaThanhToan'));
    }
}
