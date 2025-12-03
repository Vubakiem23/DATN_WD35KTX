<?php

namespace App\Http\Controllers;

use App\Models\HoaDonUtilitiesPayment;
use App\Models\Phong;
use App\Models\Khu; // <-- THÊM DÒNG NÀY
use Illuminate\Http\Request;

class ThongBaoHoaDonDienNuocController extends Controller
{
    /**
     * TRANG TỔNG QUAN – DANH SÁCH PHÒNG
     */
    public function index(Request $request)
    {
        $phongId = $request->query('phong_id');
        $khuId   = $request->query('khu_id'); // <-- THÊM LỌC KHU

        // DANH SÁCH KHU CHO DROPDOWN
        $khus = Khu::orderBy('ten_khu')->get();

        // DANH SÁCH PHÒNG CHO DROPDOWN (phụ thuộc khu nếu có chọn)
        $phongsDropdown = Phong::query()->orderBy('ten_phong');
        if ($khuId) {
            $phongsDropdown->where('khu_id', $khuId);
        }
        $phongs = $phongsDropdown->get();

        // Query các phòng cần hiển thị + thống kê
        $phongsQuery = Phong::with(['hoaDons.utilitiesPayments'])
            ->orderBy('ten_phong');

        // Lọc theo khu
        if ($khuId) {
            $phongsQuery->where('khu_id', $khuId);
        }

        // Lọc theo phòng
        if ($phongId) {
            $phongsQuery->where('id', $phongId);
        }

        // Paginate 10 phòng mỗi trang
        $phongsData = $phongsQuery->paginate(10)->appends($request->query());

        // Map dữ liệu tổng hợp trên collection của paginator
        $dataCollection = $phongsData->getCollection()->map(function ($phong) {
            $payments = $phong->hoaDons->flatMap->utilitiesPayments;

            return (object)[
                'phong'           => $phong,
                'tong_tien'       => $payments->sum('tong_tien'),
                'da_thanh_toan'   => $payments->where('da_thanh_toan', 1)->count(),
                'chua_thanh_toan' => $payments->where('da_thanh_toan', 0)->count(),
            ];
        });

        // Gắn lại collection đã map vào paginator
        $phongsData->setCollection($dataCollection);

        return view('thongbao_diennuoc.index', [
            'data'    => $phongsData, // paginator
            'phongs'  => $phongs,     // dùng cho select phòng
            'khus'    => $khus,       // dùng cho select khu
            'phongId' => $phongId,
            'khuId'   => $khuId,
        ]);
    }

    /**
     * TRANG CHI TIẾT MỘT PHÒNG
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
