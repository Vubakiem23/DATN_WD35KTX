<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\Phong;
use App\Models\Slot;
use App\Models\LichBaoTri;
use App\Models\SinhVien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        
        // 3. Tổng tiền thu được
        // 3.1. Phí phòng: tính theo tiền phòng trong hóa đơn đã thanh toán tháng hiện tại
        $tongTienPhong = HoaDon::where('thang', $selectedMonth)
            ->where('da_thanh_toan', true)
            ->sum(DB::raw('COALESCE(tien_phong_slot,0)'));
        
        // Nếu muốn tính chính xác hơn, có thể join với bảng thanh toán phí phòng (nếu có)
        // Hiện tại tính dựa trên slot đang có sinh viên
        
        // 3.2. Tiền điện + nước từ hóa đơn đã thanh toán trong tháng
        $tongTienDienNuoc = HoaDon::where('thang', $selectedMonth)
            ->where('da_thanh_toan', true)
            ->sum('thanh_tien') ?? 0;
        
        $tongTienThuDuoc = $tongTienPhong + $tongTienDienNuoc;
        
        // 4. Tổng chi phí bảo trì - sửa chữa
        // Chi phí từ sự cố đã thanh toán
        $tongChiPhiSuCo = SuCo::whereBetween('ngay_gui', [$monthStart, $monthEnd])
            ->where('is_paid', true)
            ->sum('payment_amount') ?? 0;
        
        // Chi phí từ lịch bảo trì (nếu có trường chi phí, tạm thời dùng 0)
        // Có thể thêm trường chi_phi vào LichBaoTri sau
        $tongChiPhiBaoTri = 0; // Tạm thời
        
        $tongChiPhiBaoTriSuaChua = $tongChiPhiSuCo + $tongChiPhiBaoTri;
        
        // 5. Số slot đang trống và đang có người ở
        $tongSoSlot = Slot::count();
        $soSlotCoNguoiO = Slot::whereNotNull('sinh_vien_id')->count();
        $soSlotTrong = $tongSoSlot - $soSlotCoNguoiO;
        
        // Thống kê theo tháng (12 tháng gần nhất)
        $thongKeTheoThang = [];
        for ($i = 11; $i >= 0; $i--) {
            $thang = Carbon::now()->subMonths($i);
            $thangStr = $thang->format('Y-m');
            $thangStart = $thang->copy()->startOfMonth();
            $thangEnd = $thang->copy()->endOfMonth();
            
            $thongKeTheoThang[] = [
                'thang' => $thang->format('m/Y'),
                'su_co_phat_sinh' => SuCo::whereBetween('ngay_gui', [$thangStart, $thangEnd])->count(),
                'su_co_da_xu_ly' => SuCo::whereBetween('ngay_gui', [$thangStart, $thangEnd])
                    ->where('trang_thai', 'resolved')
                    ->count(),
                'tong_tien_thu' => $this->tinhTongTienThuTheoThang($thangStr),
                'tong_chi_phi' => SuCo::whereBetween('ngay_gui', [$thangStart, $thangEnd])
                    ->where('is_paid', true)
                    ->sum('payment_amount') ?? 0,
            ];
        }
        
        // Tỷ lệ xử lý sự cố
        $tyLeXuLySuCo = $soSuCoPhatSinh > 0 
            ? round(($soSuCoDaXuLy / $soSuCoPhatSinh) * 100, 2) 
            : 0;
        
        // Lợi nhuận ròng
        $loiNhuanRong = $tongTienThuDuoc - $tongChiPhiBaoTriSuaChua;
        
        // 6. Thống kê hồ sơ sinh viên (tổng tất cả hồ sơ)
        $tongHoSo = SinhVien::count(); // Tổng số hồ sơ được gửi
        $daDuyet = SinhVien::where('trang_thai_ho_so', 'Đã duyệt')->count(); // Hồ sơ đã duyệt
        $choDuyet = SinhVien::where('trang_thai_ho_so', 'Chờ duyệt')->count(); // Hồ sơ chờ duyệt
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
            'tyLeDuyet',
            'soTaiSanDaBaoTri',
            'soTaiSanDangBaoTri',
            'soTaiSanHoanThanhBaoTri',
            'tyLeHoanThanhBaoTri'
        ));
    }
    
    /**
     * Tính tổng tiền thu được theo tháng
     */
    private function tinhTongTienThuTheoThang($thang)
    {
        // Phí phòng: tính theo tiền phòng trong hóa đơn đã thanh toán tháng này
        $tongTienPhong = HoaDon::where('thang', $thang)
            ->where('da_thanh_toan', true)
            ->sum(DB::raw('COALESCE(tien_phong_slot,0)'));
        
        // Tiền điện nước từ hóa đơn đã thanh toán trong tháng
        $tongTienDienNuoc = HoaDon::where('thang', $thang)
            ->where('da_thanh_toan', true)
            ->sum('thanh_tien') ?? 0;
        
        return $tongTienPhong + $tongTienDienNuoc;
    }
}
