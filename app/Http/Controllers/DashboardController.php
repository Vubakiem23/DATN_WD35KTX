<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\Phong;
use App\Models\Slot;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardExport;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard báo cáo thống kê
     */
    public function index(Request $request)
    {
        // Lấy tháng và năm từ request, mặc định là tháng hiện tại
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Tính toán các chỉ số
        $stats = $this->calculateStats($month, $year);
        
        // Dữ liệu cho biểu đồ theo tháng (12 tháng gần nhất)
        $monthlyData = $this->getMonthlyData($year);
        
        return view('dashboard.index', compact('stats', 'monthlyData', 'month', 'year'));
    }

    /**
     * Tính toán các chỉ số thống kê cho tháng/năm cụ thể
     */
    private function calculateStats($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        // 1. Số sự cố phát sinh trong tháng
        $soSuCoPhatSinh = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])->count();
        
        // 2. Số sự cố đã xử lý (trạng thái = 'Hoàn thành')
        $soSuCoDaXuLy = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
            ->where('trang_thai', 'Hoàn thành')
            ->count();
        
        // 3. Tổng tiền thu được (phí phòng + điện + nước) - chỉ tính hóa đơn đã thanh toán
        $tongTienThuDuoc = HoaDon::whereBetween('created_at', [$startDate, $endDate])
            ->where('da_thanh_toan', true)
            ->get()
            ->sum(function ($hoaDon) {
                $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
                $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
                $gia_phong = optional($hoaDon->phong)->gia_phong ?? 0;
                $tien_dien = $so_dien * $hoaDon->don_gia_dien;
                $tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
                return $tien_dien + $tien_nuoc + $gia_phong;
            });
        
        // 4. Tổng chi phí bảo trì – sửa chữa (từ sự cố đã thanh toán)
        $tongChiPhiBaoTri = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
            ->where('is_paid', true)
            ->sum('payment_amount');
        
        // 5. Số slot đang trống và đang có người ở
        $tongSlot = Slot::count();
        $slotDangCoNguoiO = Slot::whereNotNull('sinh_vien_id')->count();
        $slotDangTrong = $tongSlot - $slotDangCoNguoiO;
        
        // Tính phần trăm
        $phanTramSlotTrong = $tongSlot > 0 ? round(($slotDangTrong / $tongSlot) * 100, 1) : 0;
        $phanTramSlotCoNguoi = $tongSlot > 0 ? round(($slotDangCoNguoiO / $tongSlot) * 100, 1) : 0;
        
        // Tỷ lệ xử lý sự cố
        $tyLeXuLy = $soSuCoPhatSinh > 0 ? round(($soSuCoDaXuLy / $soSuCoPhatSinh) * 100, 1) : 0;
        
        // 6. Thống kê hồ sơ sinh viên (tổng tất cả hồ sơ)
        $tongHoSo = SinhVien::count(); // Tổng số hồ sơ được gửi
        $daDuyet = SinhVien::where('trang_thai_ho_so', 'Đã duyệt')->count(); // Hồ sơ đã duyệt
        $choDuyet = SinhVien::where('trang_thai_ho_so', 'Chờ duyệt')->count(); // Hồ sơ chờ duyệt
        $chuaDuyet = $tongHoSo - $daDuyet; // Hồ sơ chưa duyệt = Tổng - Đã duyệt
        
        return [
            'so_su_co_phat_sinh' => $soSuCoPhatSinh,
            'so_su_co_da_xu_ly' => $soSuCoDaXuLy,
            'tong_tien_thu_duoc' => $tongTienThuDuoc,
            'tong_chi_phi_bao_tri' => $tongChiPhiBaoTri,
            'slot_dang_trong' => $slotDangTrong,
            'slot_dang_co_nguoi_o' => $slotDangCoNguoiO,
            'tong_slot' => $tongSlot,
            'phan_tram_slot_trong' => $phanTramSlotTrong,
            'phan_tram_slot_co_nguoi' => $phanTramSlotCoNguoi,
            'ty_le_xu_ly' => $tyLeXuLy,
            'tong_ho_so' => $tongHoSo,
            'ho_so_da_duyet' => $daDuyet,
            'ho_so_chua_duyet' => $chuaDuyet,
            'ho_so_cho_duyet' => $choDuyet,
            'ty_le_duyet' => $tongHoSo > 0 ? round(($daDuyet / $tongHoSo) * 100, 1) : 0,
            'thang' => $month,
            'nam' => $year,
        ];
    }

    /**
     * Lấy dữ liệu theo tháng cho biểu đồ (12 tháng gần nhất)
     */
    private function getMonthlyData($year)
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $yearMonth = $date->year;
            
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            
            $suCoPhatSinh = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])->count();
            $suCoDaXuLy = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
                ->where('trang_thai', 'Hoàn thành')
                ->count();
            
            $tienThuDuoc = HoaDon::whereBetween('created_at', [$startDate, $endDate])
                ->where('da_thanh_toan', true)
                ->get()
                ->sum(function ($hoaDon) {
                    $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
                    $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
                    $gia_phong = optional($hoaDon->phong)->gia_phong ?? 0;
                    $tien_dien = $so_dien * $hoaDon->don_gia_dien;
                    $tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
                    return $tien_dien + $tien_nuoc + $gia_phong;
                });
            
            $chiPhiBaoTri = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
                ->where('is_paid', true)
                ->sum('payment_amount');
            
            $data[] = [
                'thang' => $date->format('m/Y'),
                'thang_label' => $date->format('M/Y'),
                'su_co_phat_sinh' => $suCoPhatSinh,
                'su_co_da_xu_ly' => $suCoDaXuLy,
                'tien_thu_duoc' => $tienThuDuoc,
                'chi_phi_bao_tri' => $chiPhiBaoTri,
            ];
        }
        
        return $data;
    }

    /**
     * Export báo cáo ra Excel
     */
    public function export(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $fileName = "BaoCao_ThongKe_{$month}_{$year}.xlsx";
        
        return Excel::download(new DashboardExport($month, $year), $fileName);
    }
}

