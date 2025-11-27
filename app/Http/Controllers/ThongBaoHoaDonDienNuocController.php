<?php

namespace App\Http\Controllers;

use App\Models\HoaDonUtilitiesPayment;
use App\Models\Phong;
use Illuminate\Http\Request;

class ThongBaoHoaDonDienNuocController extends Controller
{
    /**
     * ---------------------------------------------
     *  TRANG TỔNG QUAN – DANH SÁCH PHÒNG
     * ---------------------------------------------
     */
    public function index(Request $request)
    {
        $phongId = $request->query('phong_id');

        // Lấy tất cả phòng để hiển thị dropdown
        $phongs = Phong::orderBy('ten_phong')->get();

        // Lấy các phòng cần hiển thị dữ liệu
        $phongsQuery = Phong::with(['hoaDons.utilitiesPayments'])
            ->orderBy('ten_phong');

        if ($phongId) {
            $phongsQuery->where('id', $phongId);
        }

        $phongsData = $phongsQuery->get();

        // Chuẩn bị dữ liệu tổng hợp
        $data = $phongsData->map(function ($phong) {
            $payments = $phong->hoaDons->flatMap->utilitiesPayments;

            return (object)[
                'phong'           => $phong,
                'tong_tien'       => $payments->sum('tong_tien'),
                'da_thanh_toan'   => $payments->where('da_thanh_toan', 1)->count(),
                'chua_thanh_toan' => $payments->where('da_thanh_toan', 0)->count(),
            ];
        });

        return view('thongbao_diennuoc.index', [
            'data'    => $data,
            'phongs'  => $phongs,
            'phongId' => $phongId, // để giữ selected dropdown
        ]);
    }

    /**
     * ---------------------------------------------
     *  TRANG CHI TIẾT MỘT PHÒNG
     * ---------------------------------------------
     */
    public function detail($phongId)
    {
        $phong = Phong::findOrFail($phongId);

        $query = HoaDonUtilitiesPayment::with(['hoaDon', 'sinhVien'])
            ->whereHas('hoaDon', function ($q) use ($phongId) {
                $q->where('phong_id', $phongId);
            });

        $daThanhToan = (clone $query)
            ->where('da_thanh_toan', 1)
            ->orderBy('ngay_thanh_toan', 'desc')
            ->get();

        $chuaThanhToan = (clone $query)
            ->where('da_thanh_toan', 0)
            ->get();

        return view('thongbao_diennuoc.detail', compact(
            'phong',
            'daThanhToan',
            'chuaThanhToan'
        ));
    }
}
