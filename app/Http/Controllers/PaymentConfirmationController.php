<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phong;
use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use App\Traits\HoaDonCalculations;


class PaymentConfirmationController extends Controller
{
    use HoaDonCalculations;
    /**
     * Trang admin hiển thị danh sách yêu cầu xác nhận thanh toán từ sinh viên
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, slot, utilities
        $status = $request->get('status', 'all'); // all, cho_xac_nhan, da_thanh_toan, chua_thanh_toan
        $search = $request->get('search'); // Tìm kiếm theo sinh viên, phòng, hóa đơn

        // Lấy yêu cầu thanh toán tiền phòng (slot)
        $slotPaymentsQuery = HoaDonSlotPayment::with(['hoaDon.phong.khu', 'sinhVien'])
            ->whereNotNull('client_requested_at');

        // Lấy yêu cầu thanh toán điện nước
        $utilitiesPaymentsQuery = HoaDonUtilitiesPayment::with(['hoaDon.phong.khu', 'sinhVien'])
            ->whereNotNull('client_requested_at');

        // Áp dụng filter trạng thái
        if ($status !== 'all') {
            $slotPaymentsQuery->where('trang_thai', $status);
            $utilitiesPaymentsQuery->where('trang_thai', $status);
        }

        // Áp dụng filter tìm kiếm
        if ($search) {
            $searchTerm = "%{$search}%";
            $slotPaymentsQuery->whereHas('sinhVien', function ($q) use ($searchTerm) {
                $q->where('ho_ten', 'LIKE', $searchTerm)
                  ->orWhere('ma_sinh_vien', 'LIKE', $searchTerm);
            })
            ->orWhereHas('hoaDon.phong', function ($q) use ($searchTerm) {
                $q->where('ten_phong', 'LIKE', $searchTerm);
            });

            $utilitiesPaymentsQuery->whereHas('sinhVien', function ($q) use ($searchTerm) {
                $q->where('ho_ten', 'LIKE', $searchTerm)
                  ->orWhere('ma_sinh_vien', 'LIKE', $searchTerm);
            })
            ->orWhereHas('hoaDon.phong', function ($q) use ($searchTerm) {
                $q->where('ten_phong', 'LIKE', $searchTerm);
            });
        }

        // Sắp xếp theo ngày gửi mới nhất
        $slotPayments = $slotPaymentsQuery->orderByDesc('client_requested_at')->get();
        $utilitiesPayments = $utilitiesPaymentsQuery->orderByDesc('client_requested_at')->get();

        // Compute requested amount for each payment (per-student)
        foreach ($slotPayments as $payment) {
            $hoaDon = $payment->hoaDon;
            if ($hoaDon) {
                $this->enrichHoaDonWithPhongPricing($hoaDon);
                $this->attachSlotBreakdown($hoaDon);
                $breakdown = collect($hoaDon->slot_breakdowns ?? [])->firstWhere('label', $payment->slot_label);
                $payment->requested_amount = $breakdown['tien_phong'] ?? ($hoaDon->slot_unit_price ?? 0);
            } else {
                $payment->requested_amount = 0;
            }
        }

        foreach ($utilitiesPayments as $payment) {
            $hoaDon = $payment->hoaDon;
            if ($hoaDon) {
                // Calculate utilities amount from meter readings
                $so_dien = ($hoaDon->so_dien_moi ?? 0) - ($hoaDon->so_dien_cu ?? 0);
                $so_nuoc = ($hoaDon->so_nuoc_moi ?? 0) - ($hoaDon->so_nuoc_cu ?? 0);
                $hoaDon->tien_dien = max(0, $so_dien) * ($hoaDon->don_gia_dien ?? 0);
                $hoaDon->tien_nuoc = max(0, $so_nuoc) * ($hoaDon->don_gia_nuoc ?? 0);
                
                $this->enrichHoaDonWithPhongPricing($hoaDon);
                $this->attachSlotBreakdown($hoaDon);
                $breakdown = collect($hoaDon->slot_breakdowns ?? [])->firstWhere('label', $payment->slot_label);
                if ($breakdown) {
                    $payment->requested_tien_dien = $breakdown['tien_dien'] ?? 0;
                    $payment->requested_tien_nuoc = $breakdown['tien_nuoc'] ?? 0;
                    $payment->requested_amount = ($breakdown['tien_dien'] ?? 0) + ($breakdown['tien_nuoc'] ?? 0);
                } else {
                    $payment->requested_tien_dien = $payment->tien_dien ?? 0;
                    $payment->requested_tien_nuoc = $payment->tien_nuoc ?? 0;
                    $payment->requested_amount = ($payment->tien_dien ?? 0) + ($payment->tien_nuoc ?? 0);
                }
            } else {
                $payment->requested_tien_dien = 0;
                $payment->requested_tien_nuoc = 0;
                $payment->requested_amount = 0;
            }
        }

        // Lọc theo type
        if ($type === 'slot') {
            $utilitiesPayments = collect();
        } elseif ($type === 'utilities') {
            $slotPayments = collect();
        }

        // Thống kê
        $totalRequests = $slotPayments->count() + $utilitiesPayments->count();
        $pendingRequests = $slotPayments->where('trang_thai', 'cho_xac_nhan')->count() 
                         + $utilitiesPayments->where('trang_thai', 'cho_xac_nhan')->count();
        $confirmedRequests = $slotPayments->where('trang_thai', 'da_thanh_toan')->count() 
                           + $utilitiesPayments->where('trang_thai', 'da_thanh_toan')->count();

        return view('admin.payment_confirmation.index', compact(
            'slotPayments',
            'utilitiesPayments',
            'type',
            'status',
            'search',
            'totalRequests',
            'pendingRequests',
            'confirmedRequests'
        ));
    }

    /**
     * Xác nhận yêu cầu thanh toán slot
     */
    public function confirmSlotPayment(Request $request, $id)
    {
        $payment = HoaDonSlotPayment::findOrFail($id);
        
        $payment->trang_thai = 'da_thanh_toan';
        $payment->da_thanh_toan = true;
        $payment->ngay_thanh_toan = now();
        $payment->xac_nhan_boi = auth()->id();
        $payment->ghi_chu = $request->get('ghi_chu_admin', $payment->ghi_chu);
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => '✅ Đã xác nhận thanh toán tiền phòng!'
        ]);
    }

    /**
     * Xác nhận yêu cầu thanh toán điện nước
     */
    public function confirmUtilitiesPayment(Request $request, $id)
    {
        $payment = HoaDonUtilitiesPayment::findOrFail($id);
        
        $payment->trang_thai = 'da_thanh_toan';
        $payment->da_thanh_toan = true;
        $payment->ngay_thanh_toan = now();
        $payment->xac_nhan_boi = auth()->id();
        $payment->ghi_chu = $request->get('ghi_chu_admin', $payment->ghi_chu);
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => '✅ Đã xác nhận thanh toán điện nước!'
        ]);
    }

    /**
     * Từ chối yêu cầu thanh toán slot
     */
    public function rejectSlotPayment(Request $request, $id)
    {
        $payment = HoaDonSlotPayment::findOrFail($id);
        
        $payment->trang_thai = 'chua_thanh_toan';
        $payment->da_thanh_toan = false;
        $payment->ghi_chu = $request->get('ghi_chu_admin', 'Yêu cầu bị từ chối');
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => '❌ Đã từ chối yêu cầu thanh toán'
        ]);
    }

    /**
     * Từ chối yêu cầu thanh toán điện nước
     */
    public function rejectUtilitiesPayment(Request $request, $id)
    {
        $payment = HoaDonUtilitiesPayment::findOrFail($id);
        
        $payment->trang_thai = 'chua_thanh_toan';
        $payment->da_thanh_toan = false;
        $payment->ghi_chu = $request->get('ghi_chu_admin', 'Yêu cầu bị từ chối');
        $payment->save();

        return response()->json([
            'success' => true,
            'message' => '❌ Đã từ chối yêu cầu thanh toán'
        ]);
    }

    /**
     * Xử lý hành động hàng loạt (xác nhận / từ chối) cho slot hoặc utilities
     */
    public function bulkAction(Request $request)
    {
        $type = $request->get('type'); // 'slot' or 'utilities'
        $action = $request->get('action'); // 'confirm' or 'reject'
        $ids = $request->get('ids', []);
        $note = $request->get('ghi_chu_admin', null);

        if (!in_array($type, ['slot', 'utilities'])) {
            return response()->json(['success' => false, 'message' => 'Loại không hợp lệ'], 422);
        }
        if (!in_array($action, ['confirm', 'reject'])) {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ'], 422);
        }
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Chưa chọn yêu cầu nào'], 422);
        }

        $model = $type === 'slot' ? HoaDonSlotPayment::class : HoaDonUtilitiesPayment::class;

        $now = now();
        if ($action === 'confirm') {
            $updated = $model::whereIn('id', $ids)->update([
                'trang_thai' => 'da_thanh_toan',
                'da_thanh_toan' => true,
                'ngay_thanh_toan' => $now,
                'xac_nhan_boi' => auth()->id(),
                'ghi_chu' => $note,
            ]);
            return response()->json(['success' => true, 'message' => "Đã xác nhận {$updated} yêu cầu.", 'count' => $updated]);
        }

        // reject
        $updated = $model::whereIn('id', $ids)->update([
            'trang_thai' => 'chua_thanh_toan',
            'da_thanh_toan' => false,
            'ghi_chu' => $note ?? 'Yêu cầu bị từ chối',
        ]);

        return response()->json(['success' => true, 'message' => "Đã từ chối {$updated} yêu cầu.", 'count' => $updated]);
    }
    public function thongBaoHoaDonSlot(Request $request)
{
    $phongId = $request->get('phong_id'); // Lọc theo phòng nếu chọn
    $phongs = Phong::all(); // Lấy danh sách tất cả phòng

    // Query hóa đơn slot payment
    $query = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien']); // load quan hệ

    if ($phongId) {
        $query->whereHas('hoaDon', function($q) use ($phongId) {
            $q->where('phong_id', $phongId);
        });
    }

    // Phân loại đã thanh toán / chưa thanh toán
    $daThanhToan = (clone $query)->where('da_thanh_toan', 1)->get();
    $chuaThanhToan = (clone $query)->where('da_thanh_toan', 0)->get();

    return view('thongbao_hoadonslot.index', compact('phongs', 'phongId', 'daThanhToan', 'chuaThanhToan'));
}


}
