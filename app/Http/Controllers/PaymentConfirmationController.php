<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($request, $id) {
            $payment = HoaDonSlotPayment::with(['sinhVien', 'hoaDon.phong'])->findOrFail($id);
            
            $payment->trang_thai = 'da_thanh_toan';
            $payment->da_thanh_toan = true;
            $payment->ngay_thanh_toan = now();
            $payment->xac_nhan_boi = auth()->id();
            $payment->ghi_chu = $request->get('ghi_chu_admin', $payment->ghi_chu);
            $payment->save();

            // QUAN TRỌNG: Khi xác nhận thanh toán, luôn gán sinh viên vào phòng nếu có phòng trong hóa đơn
            if ($payment->sinhVien && $payment->hoaDon && $payment->hoaDon->phong) {
                $phongId = $payment->hoaDon->phong->id;
                
                // Nếu sinh viên chưa có phong_id hoặc phong_id khác với phòng trong hóa đơn
                // thì gán lại phong_id cho sinh viên
                if (empty($payment->sinhVien->phong_id) || $payment->sinhVien->phong_id != $phongId) {
                    // Gán sinh viên vào phòng
                    $payment->sinhVien->phong_id = $phongId;
                    $payment->sinhVien->save();
                }

                // Tìm hoặc cập nhật assignment
                $assignment = \App\Models\RoomAssignment::where('sinh_vien_id', $payment->sinhVien->id)
                    ->where('phong_id', $phongId)
                    ->whereNull('end_date')
                    ->latest('start_date')
                    ->first();

                if ($assignment) {
                    // Cập nhật trạng thái assignment nếu đang pending
                    if ($assignment->trang_thai == \App\Models\RoomAssignment::STATUS_PENDING_CONFIRMATION) {
                        $assignment->trang_thai = \App\Models\RoomAssignment::STATUS_CONFIRMED;
                        $assignment->save();
                    }
                }

                // Tìm hoặc tạo slot cho sinh viên
                $slot = \App\Models\Slot::where('phong_id', $phongId)
                    ->where('sinh_vien_id', $payment->sinhVien->id)
                    ->first();

                if (!$slot) {
                    // Tìm slot trống trong phòng
                    $emptySlot = \App\Models\Slot::where('phong_id', $phongId)
                        ->whereNull('sinh_vien_id')
                        ->first();
                    
                    if ($emptySlot) {
                        $emptySlot->sinh_vien_id = $payment->sinhVien->id;
                        $emptySlot->save();
                        $payment->slot_id = $emptySlot->id;
                        // Cập nhật slot_label với tên slot thực tế
                        $payment->slot_label = $emptySlot->ma_slot ?? ('Slot ' . $emptySlot->id);
                        $payment->save();
                    }
                } else {
                    $payment->slot_id = $slot->id;
                    // Cập nhật slot_label với tên slot thực tế
                    $payment->slot_label = $slot->ma_slot ?? ('Slot ' . $slot->id);
                    $payment->save();
                }

                // Cập nhật trạng thái phòng
                if (method_exists($payment->hoaDon->phong, 'updateStatusBasedOnCapacity')) {
                    $payment->hoaDon->phong->updateStatusBasedOnCapacity();
                }
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Đã xác nhận thanh toán tiền phòng! Sinh viên đã được gán vào phòng.'
            ]);
        });
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

        return DB::transaction(function () use ($type, $action, $ids, $note) {
            $model = $type === 'slot' ? HoaDonSlotPayment::class : HoaDonUtilitiesPayment::class;
            $now = now();
            $updated = 0;

            if ($action === 'confirm') {
                // Lấy tất cả payments với relationships
                $payments = $model::with(['sinhVien', 'hoaDon.phong'])->whereIn('id', $ids)->get();
                
                foreach ($payments as $payment) {
                    // Cập nhật trạng thái thanh toán
                    $payment->trang_thai = 'da_thanh_toan';
                    $payment->da_thanh_toan = true;
                    $payment->ngay_thanh_toan = $now;
                    $payment->xac_nhan_boi = auth()->id();
                    if ($note) {
                        $payment->ghi_chu = $note;
                    }
                    
                    // Nếu là slot payment, thực hiện logic gán phòng và cập nhật slot_label
                    if ($type === 'slot' && $payment instanceof HoaDonSlotPayment) {
                        // Kiểm tra xem có phòng trong hóa đơn không
                        if ($payment->sinhVien && $payment->hoaDon && $payment->hoaDon->phong) {
                            $phongId = $payment->hoaDon->phong->id;
                            
                            // QUAN TRỌNG: Nếu sinh viên chưa có phong_id hoặc phong_id khác với phòng trong hóa đơn
                            // thì gán lại phong_id cho sinh viên
                            if (empty($payment->sinhVien->phong_id) || $payment->sinhVien->phong_id != $phongId) {
                                // Gán sinh viên vào phòng
                                $payment->sinhVien->phong_id = $phongId;
                                $payment->sinhVien->save();
                            }

                            // Tìm hoặc cập nhật assignment
                            $assignment = \App\Models\RoomAssignment::where('sinh_vien_id', $payment->sinhVien->id)
                                ->where('phong_id', $phongId)
                                ->whereNull('end_date')
                                ->latest('start_date')
                                ->first();

                            if ($assignment) {
                                // Cập nhật trạng thái assignment nếu đang pending
                                if ($assignment->trang_thai == \App\Models\RoomAssignment::STATUS_PENDING_CONFIRMATION) {
                                    $assignment->trang_thai = \App\Models\RoomAssignment::STATUS_CONFIRMED;
                                    $assignment->save();
                                }
                            }

                            // Tìm hoặc tạo slot cho sinh viên
                            $slot = \App\Models\Slot::where('phong_id', $phongId)
                                ->where('sinh_vien_id', $payment->sinhVien->id)
                                ->first();

                            if (!$slot) {
                                // Tìm slot trống trong phòng
                                $emptySlot = \App\Models\Slot::where('phong_id', $phongId)
                                    ->whereNull('sinh_vien_id')
                                    ->first();
                                
                                if ($emptySlot) {
                                    $emptySlot->sinh_vien_id = $payment->sinhVien->id;
                                    $emptySlot->save();
                                    $payment->slot_id = $emptySlot->id;
                                    // Cập nhật slot_label với tên slot thực tế
                                    $payment->slot_label = $emptySlot->ma_slot ?? ('Slot ' . $emptySlot->id);
                                }
                            } else {
                                $payment->slot_id = $slot->id;
                                // Cập nhật slot_label với tên slot thực tế
                                $payment->slot_label = $slot->ma_slot ?? ('Slot ' . $slot->id);
                            }

                            // Cập nhật trạng thái phòng
                            if (method_exists($payment->hoaDon->phong, 'updateStatusBasedOnCapacity')) {
                                $payment->hoaDon->phong->updateStatusBasedOnCapacity();
                            }
                        } else {
                            // Nếu đã có slot_id nhưng không có phòng trong hóa đơn, cập nhật slot_label từ slot đó
                            if ($payment->slot_id) {
                                $slot = \App\Models\Slot::find($payment->slot_id);
                                if ($slot) {
                                    $payment->slot_label = $slot->ma_slot ?? ('Slot ' . $slot->id);
                                }
                            }
                        }
                    }
                    
                    $payment->save();
                    $updated++;
                }
                
                return response()->json([
                    'success' => true, 
                    'message' => "Đã xác nhận {$updated} yêu cầu.", 
                    'count' => $updated
                ]);
            }

            // reject
            $payments = $model::whereIn('id', $ids)->get();
            foreach ($payments as $payment) {
                $payment->trang_thai = 'chua_thanh_toan';
                $payment->da_thanh_toan = false;
                $payment->ghi_chu = $note ?? 'Yêu cầu bị từ chối';
                $payment->save();
                $updated++;
            }

            return response()->json([
                'success' => true, 
                'message' => "Đã từ chối {$updated} yêu cầu.", 
                'count' => $updated
            ]);
        });
    }
//     public function thongBaoHoaDonSlot(Request $request)
// {
//     $phongId = $request->get('phong_id'); // Lọc theo phòng nếu chọn
//     $phongs = Phong::all(); // Lấy danh sách tất cả phòng

//     // Query hóa đơn slot payment
//     $query = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien']); // load quan hệ

//     if ($phongId) {
//         $query->whereHas('hoaDon', function($q) use ($phongId) {
//             $q->where('phong_id', $phongId);
//         });
//     }

//     // Phân loại đã thanh toán / chưa thanh toán
//     $daThanhToan = (clone $query)->where('da_thanh_toan', 1)->get();
//     $chuaThanhToan = (clone $query)->where('da_thanh_toan', 0)->get();

//     return view('thongbao_hoadonslot.index', compact('phongs', 'phongId', 'daThanhToan', 'chuaThanhToan'));
// }
public function thongBaoHoaDonSlot(Request $request)
{
    // Lấy giá trị filter từ request
    $phongId = $request->get('phong_id');
    $maSV    = $request->get('ma_sinh_vien');
    $hoTen   = $request->get('ho_ten');
    $status  = $request->get('status', 'all'); // all | da_thanh_toan | chua_thanh_toan
    $search  = $request->get('search');

    // Lấy danh sách phòng để hiển thị filter
    $phongs = Phong::all();

    // =========================
    // KHỞI TẠO BASE QUERY CHUNG
    // =========================
    $baseQuery = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien']);

    // Lọc theo phòng
    if (!empty($phongId)) {
        $baseQuery->whereHas('hoaDon', function ($q) use ($phongId) {
            $q->where('phong_id', $phongId);
        });
    }

    // Lọc theo mã sinh viên
    if (!empty($maSV)) {
        $baseQuery->whereHas('sinhVien', function ($q) use ($maSV) {
            $q->where('ma_sinh_vien', 'LIKE', "%{$maSV}%");
        });
    }

    // Lọc theo họ tên sinh viên
    if (!empty($hoTen)) {
        $baseQuery->whereHas('sinhVien', function ($q) use ($hoTen) {
            $q->where('ho_ten', 'LIKE', "%{$hoTen}%");
        });
    }

    // Lọc search tổng hợp: tên sinh viên, mã sinh viên, phòng
    if (!empty($search)) {
        $baseQuery->where(function($q) use ($search) {
            $q->whereHas('sinhVien', function($q2) use ($search) {
                $q2->where('ma_sinh_vien', 'like', "%{$search}%")
                   ->orWhere('ho_ten', 'like', "%{$search}%");
            })
            ->orWhereHas('hoaDon.phong', function($q2) use ($search) {
                $q2->where('ten_phong', 'like', "%{$search}%");
            });
        });
    }

    // =========================
    // TÁCH 2 QUERY RIÊNG & PAGINATE
    // =========================

    // ĐÃ THANH TOÁN
    $daQuery = clone $baseQuery;
    $daQuery->where('da_thanh_toan', 1);

    // Nếu muốn status lọc chỉ còn nhóm này trên BE thì cũng có thể dùng:
    if ($status === 'chua_thanh_toan') {
        // user chọn "Chưa thanh toán" => cho danh sách đã thanh toán rỗng
        $daThanhToan = (new \Illuminate\Pagination\LengthAwarePaginator(
            [], 0, 20, request()->query('page_da', 1), ['path' => url()->current(), 'pageName' => 'page_da']
        ));
    } else {
        $daThanhToan = $daQuery
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'page_da');
    }

    // CHƯA THANH TOÁN
    $chuaQuery = clone $baseQuery;
    $chuaQuery->where('da_thanh_toan', 0);

    if ($status === 'da_thanh_toan') {
        // user chọn "Đã thanh toán" => cho danh sách chưa thanh toán rỗng
        $chuaThanhToan = (new \Illuminate\Pagination\LengthAwarePaginator(
            [], 0, 20, request()->query('page_chua', 1), ['path' => url()->current(), 'pageName' => 'page_chua']
        ));
    } else {
        $chuaThanhToan = $chuaQuery
            ->orderByDesc('id')
            ->paginate(20, ['*'], 'page_chua');
    }

    // Truyền dữ liệu ra view
    return view('thongbao_hoadonslot.index', compact(
        'phongs',
        'phongId',
        'maSV',
        'hoTen',
        'status',
        'search',
        'daThanhToan',
        'chuaThanhToan'
    ));
}

}
