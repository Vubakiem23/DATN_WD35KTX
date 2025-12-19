<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\Phong;
use App\Models\Slot;
use App\Models\LichBaoTri;
use App\Models\SinhVien;
use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use App\Models\Violation;
use Carbon\Carbon;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tháng được chọn (mặc định là tháng hiện tại)
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $monthStart = Carbon::parse($selectedMonth)->startOfMonth();
        $monthEnd = Carbon::parse($selectedMonth)->endOfMonth();
        
        // 1. Số sự cố phát sinh trong tháng
        $soSuCoPhatSinh = SuCo::whereBetween('ngay_gui', [$monthStart, $monthEnd])->count();
        
        // 2. Số sự cố đã xử lý (trạng thái Hoàn thành)
        $soSuCoDaXuLy = SuCo::whereBetween('ngay_gui', [$monthStart, $monthEnd])
            ->where('trang_thai', 'Hoàn thành')
            ->count();
        
        // 3. Tổng tiền thu được - Tính từ hệ thống thanh toán mới
        // 3.1. Tiền phòng: Từ HoaDonSlotPayment đã thanh toán trong tháng
        $tongTienPhong = $this->tinhTongTienPhongTuSlotPayments($monthStart, $monthEnd);
        
        // 3.2. Tiền điện nước: Từ HoaDonUtilitiesPayment đã thanh toán trong tháng
        $tongTienDienNuoc = $this->tinhTongTienDienNuocTuUtilitiesPayments($monthStart, $monthEnd);
        
        // 3.3. Tiền vi phạm: Từ Violation đã thanh toán (status = resolved) trong tháng
        $tongTienViPham = Violation::where('status', 'resolved')
            ->whereNotNull('client_paid_at')
            ->whereBetween('client_paid_at', [$monthStart, $monthEnd])
            ->sum('penalty_amount') ?? 0;
        
        $tongTienThuDuoc = $tongTienPhong + $tongTienDienNuoc + $tongTienViPham;
        
        // 4. Tổng chi phí bảo trì - sửa chữa
        // Chi phí từ sự cố đã thanh toán (dựa trên ngày thanh toán, không phải ngày gửi)
        // Logic: Nếu payment_amount = 0 → admin trả toàn bộ chi_phi_thuc_te
        //        Nếu payment_amount > 0 → admin trả chi_phi_thuc_te - payment_amount (phần chênh lệch)
        $tongChiPhiSuCo = 0;
        $suCosDaThanhToan = SuCo::where('is_paid', true)
            ->whereNotNull('ngay_thanh_toan')
            ->whereBetween('ngay_thanh_toan', [$monthStart, $monthEnd])
            ->get();
        
        foreach ($suCosDaThanhToan as $suCo) {
            $chiPhiThucTe = (float) ($suCo->chi_phi_thuc_te ?? 0);
            $paymentAmount = (float) ($suCo->payment_amount ?? 0);
            
            if ($paymentAmount == 0) {
                // Admin trả toàn bộ chi phí thực tế
                $tongChiPhiSuCo += $chiPhiThucTe;
            } else {
                // Admin trả phần chênh lệch (chi phí thực tế - số tiền sinh viên đã trả)
                $tongChiPhiSuCo += max(0, $chiPhiThucTe - $paymentAmount);
            }
        }
        
        // Chi phí từ lịch bảo trì (tính từ các lịch bảo trì đã hoàn thành trong tháng)
        $tongChiPhiBaoTri = LichBaoTri::where('trang_thai', 'Hoàn thành')
            ->whereNotNull('ngay_hoan_thanh')
            ->whereBetween('ngay_hoan_thanh', [$monthStart, $monthEnd])
            ->sum('chi_phi') ?? 0;
        
        $tongChiPhiBaoTriSuaChua = $tongChiPhiSuCo + $tongChiPhiBaoTri;
        
        // 5. Số slot đang trống và đang có người ở
        $tongSoSlot = Slot::count();
        $soSlotCoNguoiO = Slot::whereNotNull('sinh_vien_id')->count();
        $soSlotTrong = $tongSoSlot - $soSlotCoNguoiO;
        
        // Thống kê theo tháng (12 tháng gần nhất)
        $thongKeTheoThang = [];
        for ($i = 11; $i >= 0; $i--) {
            $thang = Carbon::now()->subMonths($i);
            $thangStart = $thang->copy()->startOfMonth();
            $thangEnd = $thang->copy()->endOfMonth();
            
            $thongKeTheoThang[] = [
                'thang' => $thang->format('m/Y'),
                'su_co_phat_sinh' => SuCo::whereBetween('ngay_gui', [$thangStart, $thangEnd])->count(),
                'su_co_da_xu_ly' => SuCo::whereBetween('ngay_gui', [$thangStart, $thangEnd])
                    ->where('trang_thai', 'Hoàn thành')
                    ->count(),
                'tong_tien_thu' => $this->tinhTongTienThuTheoThang($thangStart, $thangEnd),
                'tong_chi_phi' => $this->tinhTongChiPhiTheoThang($thangStart, $thangEnd),
            ];
        }
        
        // Tỷ lệ xử lý sự cố
        $tyLeXuLySuCo = $soSuCoPhatSinh > 0 
            ? round(($soSuCoDaXuLy / $soSuCoPhatSinh) * 100, 2) 
            : 0;
        
        // Lợi nhuận ròng
        $loiNhuanRong = $tongTienThuDuoc - $tongChiPhiBaoTriSuaChua;
        
        // 6. Thống kê hồ sơ sinh viên (theo tháng được chọn - dựa trên created_at hoặc updated_at)
        $tongHoSo = SinhVien::whereBetween('created_at', [$monthStart, $monthEnd])->count(); // Tổng số hồ sơ được gửi trong tháng
        $daDuyet = SinhVien::where('trang_thai_ho_so', SinhVien::STATUS_APPROVED)
            ->whereBetween('updated_at', [$monthStart, $monthEnd])->count(); // Hồ sơ đã duyệt trong tháng
        $choDuyet = SinhVien::where('trang_thai_ho_so', SinhVien::STATUS_PENDING_APPROVAL)
            ->whereBetween('created_at', [$monthStart, $monthEnd])->count(); // Hồ sơ chờ duyệt trong tháng
        $choXacNhan = SinhVien::where('trang_thai_ho_so', SinhVien::STATUS_PENDING_CONFIRMATION)
            ->whereBetween('created_at', [$monthStart, $monthEnd])->count(); // Hồ sơ chờ xác nhận trong tháng
        $chuaDuyet = $tongHoSo - $daDuyet; // Hồ sơ chưa duyệt = Tổng - Đã duyệt
        $tyLeDuyet = $tongHoSo > 0 ? round(($daDuyet / $tongHoSo) * 100, 1) : 0; // Tỷ lệ duyệt
        
        // 7. Thống kê bảo trì tài sản (trong tháng được chọn)
        // Số tài sản đã bảo trì: Tổng số lịch bảo trì có trạng thái 'Đang bảo trì' hoặc 'Hoàn thành' trong tháng
        $soTaiSanDaBaoTri = LichBaoTri::whereBetween('ngay_bao_tri', [$monthStart, $monthEnd])
            ->whereIn('trang_thai', ['Đang bảo trì', 'Hoàn thành'])
            ->count();
        
        // Số tài sản đang bảo trì: Số lịch bảo trì có trạng thái 'Đang bảo trì' trong tháng
        $soTaiSanDangBaoTri = LichBaoTri::whereBetween('ngay_bao_tri', [$monthStart, $monthEnd])
            ->where('trang_thai', 'Đang bảo trì')
            ->count();
        
        // Số tài sản đã hoàn thành bảo trì: Số lịch bảo trì có trạng thái 'Hoàn thành' trong tháng
        $soTaiSanHoanThanhBaoTri = LichBaoTri::whereBetween('ngay_bao_tri', [$monthStart, $monthEnd])
            ->where('trang_thai', 'Hoàn thành')
            ->count();
        
        // Tỷ lệ hoàn thành bảo trì
        $tyLeHoanThanhBaoTri = $soTaiSanDaBaoTri > 0 
            ? round(($soTaiSanHoanThanhBaoTri / $soTaiSanDaBaoTri) * 100, 1) 
            : 0;
        
        return view('admin.pages.index', compact(
            'selectedMonth',
            'soSuCoPhatSinh',
            'soSuCoDaXuLy',
            'tongTienThuDuoc',
            'tongTienPhong',
            'tongTienDienNuoc',
            'tongTienViPham',
            'tongChiPhiBaoTriSuaChua',
            'tongChiPhiSuCo',
            'tongChiPhiBaoTri',
            'soSlotTrong',
            'soSlotCoNguoiO',
            'tongSoSlot',
            'thongKeTheoThang',
            'tyLeXuLySuCo',
            'loiNhuanRong',
            'tongHoSo',
            'daDuyet',
            'chuaDuyet',
            'choDuyet',
            'choXacNhan',
            'tyLeDuyet',
            'soTaiSanDaBaoTri',
            'soTaiSanDangBaoTri',
            'soTaiSanHoanThanhBaoTri',
            'tyLeHoanThanhBaoTri'
        ));
    }
    
    /**
     * Tính tổng tiền thu được theo tháng (dựa trên ngày thanh toán)
     */
    private function tinhTongTienThuTheoThang($monthStart, $monthEnd)
    {
        // Tiền phòng từ slot payments
        $tongTienPhong = $this->tinhTongTienPhongTuSlotPayments($monthStart, $monthEnd);
        
        // Tiền điện nước từ utilities payments
        $tongTienDienNuoc = $this->tinhTongTienDienNuocTuUtilitiesPayments($monthStart, $monthEnd);
        
        // Tiền vi phạm đã thanh toán
        $tongTienViPham = Violation::where('status', 'resolved')
            ->whereNotNull('client_paid_at')
            ->whereBetween('client_paid_at', [$monthStart, $monthEnd])
            ->sum('penalty_amount') ?? 0;
        
        return $tongTienPhong + $tongTienDienNuoc + $tongTienViPham;
    }

    /**
     * Tính tổng tiền phòng từ HoaDonSlotPayment đã thanh toán trong khoảng thời gian
     */
    private function tinhTongTienPhongTuSlotPayments($monthStart, $monthEnd): int
    {
        // Lấy các slot payment đã thanh toán trong tháng (hệ thống mới)
        $slotPayments = HoaDonSlotPayment::with(['hoaDon.phong'])
            ->where('da_thanh_toan', true)
            ->whereNotNull('ngay_thanh_toan')
            ->whereBetween('ngay_thanh_toan', [$monthStart, $monthEnd])
            ->get();

        // Tính tổng tiền phòng từ các slot payment
        // Mỗi slot payment đại diện cho 1 slot đã thanh toán
        // Số tiền mỗi slot = tien_phong_slot / slot_billing_count của hóa đơn
        $tongTien = 0;
        
        foreach ($slotPayments as $payment) {
            $hoaDon = $payment->hoaDon;
            if ($hoaDon) {
                // Tính tiền phòng cho slot này
                $tienPhongSlot = (int) ($hoaDon->tien_phong_slot ?? 0);
                $slotBillingCount = (int) ($hoaDon->slot_billing_count ?? 1);
                
                if ($slotBillingCount > 0 && $tienPhongSlot > 0) {
                    // Chia đều tiền phòng cho số slot
                    $tienMoiSlot = (int) ($tienPhongSlot / $slotBillingCount);
                    $tongTien += $tienMoiSlot;
                } else {
                    // Fallback: tính từ phòng nếu không có dữ liệu hóa đơn
                    if ($hoaDon->phong) {
                        $slotUnitPrice = $hoaDon->phong->giaSlot();
                        if ($slotUnitPrice > 0) {
                            $tongTien += $slotUnitPrice;
                        }
                    }
                }
            }
        }

        // Xử lý hóa đơn cũ (chưa có slot payments) - tính từ da_thanh_toan trên HoaDon
        $hoaDonsCu = HoaDon::with('phong')
            ->where('da_thanh_toan', true)
            ->whereNotNull('ngay_thanh_toan')
            ->whereBetween('ngay_thanh_toan', [$monthStart, $monthEnd])
            ->whereDoesntHave('slotPayments') // Chỉ lấy hóa đơn không có slot payments
            ->get();

        foreach ($hoaDonsCu as $hoaDon) {
            $tienPhongSlot = (int) ($hoaDon->tien_phong_slot ?? 0);
            if ($tienPhongSlot > 0) {
                $tongTien += $tienPhongSlot;
            } elseif ($hoaDon->phong) {
                // Tính từ phòng nếu không có tien_phong_slot
                $tongTien += (int) $hoaDon->phong->tinhTienPhongTheoSlot(true);
            }
        }

        return (int) $tongTien;
    }

    /**
     * Tính tổng tiền điện nước từ HoaDonUtilitiesPayment đã thanh toán trong khoảng thời gian
     */
    private function tinhTongTienDienNuocTuUtilitiesPayments($monthStart, $monthEnd): int
    {
        // Lấy các utilities payment đã thanh toán trong tháng (hệ thống mới)
        $utilitiesPayments = HoaDonUtilitiesPayment::where('da_thanh_toan', true)
            ->whereNotNull('ngay_thanh_toan')
            ->whereBetween('ngay_thanh_toan', [$monthStart, $monthEnd])
            ->sum('tong_tien');

        $tongTien = (int) ($utilitiesPayments ?? 0);

        // Xử lý hóa đơn cũ (chưa có utilities payments) - tính từ da_thanh_toan_dien_nuoc trên HoaDon
        $hoaDonsCu = HoaDon::where('da_thanh_toan_dien_nuoc', true)
            ->whereNotNull('ngay_thanh_toan_dien_nuoc')
            ->whereBetween('ngay_thanh_toan_dien_nuoc', [$monthStart, $monthEnd])
            ->whereDoesntHave('utilitiesPayments') // Chỉ lấy hóa đơn không có utilities payments
            ->get();

        foreach ($hoaDonsCu as $hoaDon) {
            // Tính tiền điện nước từ hóa đơn
            $so_dien = max(0, ($hoaDon->so_dien_moi ?? 0) - ($hoaDon->so_dien_cu ?? 0));
            $so_nuoc = max(0, ($hoaDon->so_nuoc_moi ?? 0) - ($hoaDon->so_nuoc_cu ?? 0));
            $tien_dien = $so_dien * ($hoaDon->don_gia_dien ?? 0);
            $tien_nuoc = $so_nuoc * ($hoaDon->don_gia_nuoc ?? 0);
            $tongTien += (int) ($tien_dien + $tien_nuoc);
        }

        return $tongTien;
    }

    /**
     * Tính tổng chi phí bảo trì - sửa chữa theo tháng
     */
    private function tinhTongChiPhiTheoThang($monthStart, $monthEnd)
    {
        // Chi phí từ sự cố
        $tongChiPhiSuCo = 0;
        $suCosDaThanhToan = SuCo::where('is_paid', true)
            ->whereNotNull('ngay_thanh_toan')
            ->whereBetween('ngay_thanh_toan', [$monthStart, $monthEnd])
            ->get();
        
        foreach ($suCosDaThanhToan as $suCo) {
            $chiPhiThucTe = (float) ($suCo->chi_phi_thuc_te ?? 0);
            $paymentAmount = (float) ($suCo->payment_amount ?? 0);
            
            if ($paymentAmount == 0) {
                // Admin trả toàn bộ chi phí thực tế
                $tongChiPhiSuCo += $chiPhiThucTe;
            } else {
                // Admin trả phần chênh lệch (chi phí thực tế - số tiền sinh viên đã trả)
                $tongChiPhiSuCo += max(0, $chiPhiThucTe - $paymentAmount);
            }
        }
        
        // Chi phí từ lịch bảo trì
        $tongChiPhiBaoTri = LichBaoTri::where('trang_thai', 'Hoàn thành')
            ->whereNotNull('ngay_hoan_thanh')
            ->whereBetween('ngay_hoan_thanh', [$monthStart, $monthEnd])
            ->sum('chi_phi') ?? 0;
        
        return $tongChiPhiSuCo + $tongChiPhiBaoTri;
    }
}