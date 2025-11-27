<?php

namespace App\Http\Controllers;

use App\Models\HoaDonSlotPayment;
use App\Models\Phong;
use Illuminate\Http\Request;

class ThongBaoHoaDonSlotController extends Controller
{
    public function index(Request $request)
    {
        $phongs = Phong::select('id', 'ten_phong')->get();

        $phongId   = $request->phong_id;
        $maSV      = $request->ma_sinh_vien;
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        // Đã thanh toán
        $daThanhToan = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien'])
            ->where('da_thanh_toan', true)
            ->when($phongId, fn($q) => $q->whereHas('hoaDon', fn($h) => $h->where('phong_id', $phongId)))
            ->when($maSV, fn($q) => $q->whereHas('sinhVien', fn($sv) => $sv->search($maSV)))
            ->when($dateStart, fn($q) => $q->whereDate('ngay_thanh_toan', '>=', $dateStart))
            ->when($dateEnd, fn($q) => $q->whereDate('ngay_thanh_toan', '<=', $dateEnd))
            ->orderByDesc('ngay_thanh_toan')
            ->get();

        // Chưa thanh toán
        $chuaThanhToan = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien'])
            ->where('da_thanh_toan', false)
            ->when($phongId, fn($q) => $q->whereHas('hoaDon', fn($h) => $h->where('phong_id', $phongId)))
            ->when($maSV, fn($q) => $q->whereHas('sinhVien', fn($sv) => $sv->search($maSV)))
            ->when($dateStart, fn($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn($q) => $q->whereDate('created_at', '<=', $dateEnd))
            ->orderByDesc('created_at')
            ->get();

        return view('client.thongbao_hoadonslot', compact(
            'daThanhToan',
            'chuaThanhToan',
            'phongs',
            'phongId',
            'maSV',
            'dateStart',
            'dateEnd'
        ));
    }
}
