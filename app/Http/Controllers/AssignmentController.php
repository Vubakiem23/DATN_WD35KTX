<?php

namespace App\Http\Controllers;

use App\Models\Phong;
use App\Models\SinhVien;
use App\Models\RoomAssignment;
use App\Models\HoaDon;
use App\Models\HoaDonSlotPayment;
use App\Models\ThongBaoSinhVien;
use App\Models\ThongBaoPhongSv;
use App\Mail\RoomAssignmentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // tùy môi trường dev có thể tắt
    }

    /**
     * Hiển thị form gán (tùy chọn)
     */
    public function showAssignForm($svId)
    {
        $sv = SinhVien::findOrFail($svId);
        $phongs = Phong::where('trang_thai', '!=', 'Bảo trì')->get();
        return view('assignment.assign', compact('sv', 'phongs'));
    }

    /**
     * Thực hiện gán sinh viên vào phòng (transaction + lockForUpdate)
     */
    public function assign(Request $request, $svId)
    {
        $request->validate(['phong_id' => 'required|exists:phong,id']);

        return DB::transaction(function () use ($request, $svId) {
            $sv = SinhVien::lockForUpdate()->findOrFail($svId);
            $phong = Phong::lockForUpdate()->with('khu')->findOrFail($request->phong_id);
            
            // LOG: Ghi lại trạng thái ban đầu
            \Log::info("Bắt đầu gán phòng cho sinh viên", [
                'sinh_vien_id' => $sv->id,
                'phong_id_request' => $request->phong_id,
                'phong_id_hien_tai' => $sv->phong_id
            ]);

            // Kiểm tra hồ sơ đã duyệt (dựa trên cột trang_thai_ho_so)
            if (isset($sv->trang_thai_ho_so) && $sv->trang_thai_ho_so !== 'Đã duyệt') {
                return redirect()->back()->with('error', 'Hồ sơ sinh viên chưa được duyệt, không thể gán.');
            }

            // Nếu phòng bảo trì
            if ($phong->trang_thai === 'Bảo trì') {
                return redirect()->back()->with('error', 'Phòng đang bảo trì, không thể gán.');
            }

            // Kiểm tra giới tính theo khu (nếu có) hoặc theo phòng
            $requiredGender = $phong->khu && $phong->khu->gioi_tinh ? $phong->khu->gioi_tinh : $phong->gioi_tinh;
            if (!empty($requiredGender) && $requiredGender !== 'Cả hai') {
                if (trim(mb_strtolower($requiredGender)) !== trim(mb_strtolower($sv->gioi_tinh))) {
                    return redirect()->back()->with('error', 'Giới tính sinh viên không phù hợp với quy định khu/phòng.');
                }
            }

            // Kiểm tra sức chứa: chỉ đếm sinh viên đã xác nhận vào phòng
            $confirmedCount = RoomAssignment::where('phong_id', $phong->id)
                ->where('trang_thai', RoomAssignment::STATUS_CONFIRMED)
                ->whereNull('end_date')
                ->count();
            // Đếm cả các assignment đang chờ xác nhận
            $pendingCount = RoomAssignment::where('phong_id', $phong->id)
                ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
                ->whereNull('end_date')
                ->count();
            if (($confirmedCount + $pendingCount) >= (int)$phong->suc_chua) {
                return redirect()->back()->with('error', 'Phòng đã đầy.');
            }

            // Kiểm tra xem sinh viên đã có assignment chờ xác nhận chưa
            $existingPending = RoomAssignment::where('sinh_vien_id', $sv->id)
                ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
                ->whereNull('end_date')
                ->first();
            
            if ($existingPending) {
                return redirect()->back()->with('error', 'Sinh viên đã có yêu cầu gán phòng đang chờ xác nhận.');
            }

            // QUAN TRỌNG: Kiểm tra xem sinh viên đã có assignment đã xác nhận chưa
            // Nếu có và đang ở đúng phòng đó, không cho gán lại
            $confirmedAssignment = RoomAssignment::where('sinh_vien_id', $sv->id)
                ->where('trang_thai', RoomAssignment::STATUS_CONFIRMED)
                ->whereNull('end_date')
                ->latest('start_date')
                ->first();
            
            if ($confirmedAssignment && $confirmedAssignment->phong_id == $phong->id && $sv->phong_id == $phong->id) {
                return redirect()->back()->with('error', 'Sinh viên đã ở phòng này và đã xác nhận. Không thể gán lại.');
            }

            // Nếu sv đã ở phòng khác, tách ra trước
            if ($sv->phong_id && $sv->phong_id != $phong->id) {
                $old = Phong::lockForUpdate()->find($sv->phong_id);
                if ($old) {
                    // gỡ sv khỏi phòng cũ (chỉ gán null) và đóng lịch sử cũ
                    $sv->phong_id = null;
                    $sv->saveOrFail();
                    // đóng assignment đang mở (nếu có)
                    $openAssignment = RoomAssignment::where('sinh_vien_id', $sv->id)
                        ->whereNull('end_date')
                        ->latest('start_date')
                        ->first();
                    if ($openAssignment) {
                        $openAssignment->end_date = now()->toDateString();
                        $openAssignment->saveOrFail();
                    }
                    $old->updateStatusBasedOnCapacity();
                }
            }

            // QUAN TRỌNG: Đảm bảo phong_id = null trước khi tạo assignment mới
            // Điều này đảm bảo sinh viên phải xác nhận và thanh toán mới được gán vào phòng
            // KHÔNG BAO GIỜ gán phong_id vào sinh viên ở đây!
            if ($sv->phong_id) {
                $sv->phong_id = null;
            $sv->saveOrFail();
            }

            // ============================================
            // QUAN TRỌNG: Tạo RoomAssignment với trạng thái "Chờ xác nhận"
            // KHÔNG BAO GIỜ gán phong_id vào sinh viên ($sv->phong_id) ở đây!
            // Sinh viên phải xác nhận và thanh toán trong ClientController@confirmRoomAssignment
            // Chỉ khi đó mới gán phong_id vào sinh viên
            // ============================================
            $assignment = RoomAssignment::create([
                'sinh_vien_id' => $sv->id,
                'phong_id' => $phong->id, // Phòng được gán trong assignment, NHƯNG chưa gán vào sinh viên
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'trang_thai' => RoomAssignment::STATUS_PENDING_CONFIRMATION, // Chờ xác nhận
            ]);

            // ĐẢM BẢO: Sau khi tạo assignment, phong_id của sinh viên phải là null
            // QUAN TRỌNG: Phải query lại từ database để đảm bảo lấy dữ liệu mới nhất
            // Không dùng refresh() vì có thể vẫn lấy từ cache
            $svFresh = SinhVien::findOrFail($sv->id);
            if ($svFresh->phong_id) {
                \Log::warning("Phát hiện phong_id vẫn còn sau khi tạo assignment. Đang set về null.", [
                    'sinh_vien_id' => $sv->id,
                    'phong_id' => $svFresh->phong_id,
                    'assignment_id' => $assignment->id
                ]);
                // Sử dụng DB::table để update trực tiếp, tránh model events
                DB::table('sinh_vien')
                    ->where('id', $sv->id)
                    ->update(['phong_id' => null]);
                $svFresh = SinhVien::findOrFail($sv->id); // Query lại để verify
            }
            
            // Log để debug
            \Log::info("Đã tạo RoomAssignment cho sinh viên", [
                'sinh_vien_id' => $sv->id,
                'sinh_vien_phong_id' => $svFresh->phong_id, // Phải là null
                'assignment_id' => $assignment->id,
                'assignment_phong_id' => $assignment->phong_id,
                'assignment_trang_thai' => $assignment->trang_thai
            ]);

            // Tìm hoặc tạo hóa đơn tháng hiện tại cho phòng
            $currentMonth = Carbon::now()->format('m/Y');
            $hoaDon = HoaDon::where('phong_id', $phong->id)
                ->where('thang', $currentMonth)
                ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
                ->first();

            $slotUnitPrice = $phong->giaSlot();
            
            if (!$hoaDon) {
                $existingSlotPayments = HoaDonSlotPayment::whereHas('hoaDon', function($q) use ($phong, $currentMonth) {
                    $q->where('phong_id', $phong->id)
                      ->where('thang', $currentMonth)
                      ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG);
                })->count();
                
                $slotCount = max(1, $existingSlotPayments + 1);
                
                $hoaDon = HoaDon::create([
                    'phong_id' => $phong->id,
                    'invoice_type' => HoaDon::LOAI_TIEN_PHONG,
                    'thang' => $currentMonth,
                    'tien_phong_slot' => $slotUnitPrice * $slotCount,
                    'slot_unit_price' => $slotUnitPrice,
                    'slot_billing_count' => $slotCount,
                    'trang_thai' => 'Chưa thanh toán',
                    'da_thanh_toan' => false,
                ]);
            } else {
                $existingSlotPayments = $hoaDon->slotPayments()->count();
                $newSlotCount = $existingSlotPayments + 1;
                $hoaDon->slot_billing_count = $newSlotCount;
                $hoaDon->tien_phong_slot = $slotUnitPrice * $newSlotCount;
                $hoaDon->slot_unit_price = $slotUnitPrice;
                $hoaDon->saveOrFail();
            }
            
            // Tạo slot payment cho sinh viên (chưa thanh toán)
            HoaDonSlotPayment::create([
                'hoa_don_id' => $hoaDon->id,
                'slot_id' => null,
                'slot_label' => 'Chờ xác nhận - ' . $sv->ho_ten,
                'sinh_vien_id' => $sv->id,
                'sinh_vien_ten' => $sv->ho_ten,
                'trang_thai' => HoaDonSlotPayment::TRANG_THAI_CHUA_THANH_TOAN,
                'da_thanh_toan' => false,
            ]);

            // Tạo thông báo cho sinh viên
            try {
                ThongBaoSinhVien::create([
                    'sinh_vien_id' => $sv->id,
                    'noi_dung' => "Bạn đã được ban quản lý gán vào phòng {$phong->ten_phong}" . ($phong->khu ? " - Khu {$phong->khu->ten_khu}" : '') . ". Vui lòng xác nhận và thanh toán tiền phòng.",
                    'trang_thai' => 'Mới',
                ]);
            } catch (\Exception $e) {
                \Log::error('Lỗi tạo thông báo gán phòng: ' . $e->getMessage());
            }

            // Gửi email thông báo cho sinh viên
            if (!empty($sv->email)) {
                try {
                    $confirmationUrl = route('client.room.confirmation.show');
                    Mail::to($sv->email)->send(new RoomAssignmentMail($sv, $assignment, $confirmationUrl));
                } catch (\Throwable $e) {
                    \Log::error('Lỗi gửi email gán phòng: ' . $e->getMessage());
                }
            }

            // KIỂM TRA CUỐI CÙNG: Đảm bảo phong_id = null trước khi return
            $finalCheck = DB::table('sinh_vien')->where('id', $sv->id)->value('phong_id');
            if ($finalCheck) {
                \Log::error("CRITICAL: phong_id vẫn còn sau khi hoàn tất assignment. Đang force set về null.", [
                    'sinh_vien_id' => $sv->id,
                    'phong_id' => $finalCheck,
                    'assignment_id' => $assignment->id
                ]);
                DB::table('sinh_vien')
                    ->where('id', $sv->id)
                    ->update(['phong_id' => null]);
            }

            return redirect()->route('phong.index')->with('status', 'Đã gán sinh viên vào phòng. Sinh viên cần xác nhận và thanh toán để hoàn tất.');
        });
    }
}
