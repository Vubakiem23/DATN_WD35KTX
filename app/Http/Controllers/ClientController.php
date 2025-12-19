<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use App\Models\LichBaoTri;
use App\Models\HoaDonBaoTri;
use App\Models\Slot;
use App\Models\TaiSan;
use App\Models\Violation;
use App\Models\RoomAssignment;
use App\Models\ThongBaoPhongSv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Traits\HoaDonCalculations;


class ClientController extends Controller
{
    use HoaDonCalculations;
    /**
     * Lấy thông tin sinh viên từ user đang đăng nhập (nếu có)
     */
    protected function getSinhVien()
    {
        $user = Auth::user();

        // Thử lấy qua quan hệ user->sinhVien trước (nếu có user_id)
        if ($user->sinhVien) {
            return $user->sinhVien;
        }

        // Fallback: Tìm theo email (nếu có)
        return SinhVien::where('email', $user->email)->first();
    }

    /**
     * Trang chủ dashboard cho sinh viên
     */
    public function dashboard()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        // Nếu chưa có sinh viên, vẫn cho vào giao diện nhưng hiển thị thông báo
        if (!$sinhVien) {
            $stats = [
                'phong' => null,
                'so_su_co' => 0,
                'su_co_chua_xu_ly' => 0,
                'hoa_don_chua_thanh_toan' => 0,
            ];
            $suCoGanDay = collect([]);

            return view('client.dashboard', compact('user', 'sinhVien', 'stats', 'suCoGanDay'));
        }

        // Kiểm tra xem có assignment chờ xác nhận không
        $pendingRoomAssignment = RoomAssignment::where('sinh_vien_id', $sinhVien->id)
            ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
            ->whereNull('end_date')
            ->with(['phong' => function ($q) {
                $q->with('khu');
            }])
            ->latest('start_date')
            ->first();

        // QUAN TRỌNG: Chỉ hiển thị alert nếu chưa thanh toán
        // Kiểm tra xem có slotPayment đã thanh toán không
        if ($pendingRoomAssignment && $pendingRoomAssignment->phong) {
            $currentMonth = \Carbon\Carbon::now()->format('m/Y');
            $hoaDon = HoaDon::where('phong_id', $pendingRoomAssignment->phong_id)
                ->where('thang', $currentMonth)
                ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
                ->first();

            if ($hoaDon) {
                $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
                    ->where('sinh_vien_id', $sinhVien->id)
                    ->first();

                // Nếu đã thanh toán hoặc đang chờ xác nhận (đã submit), không hiển thị alert
                if ($slotPayment && ($slotPayment->da_thanh_toan || $slotPayment->trang_thai === HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN)) {
                    $pendingRoomAssignment = null;
                }
            }

            // Nếu đã có phong_id (đã xác nhận và thanh toán), không hiển thị alert
            // Kiểm tra $pendingRoomAssignment không null trước khi truy cập phong_id
            if ($pendingRoomAssignment && $sinhVien->phong_id && $sinhVien->phong_id == $pendingRoomAssignment->phong_id) {
                $pendingRoomAssignment = null;
            }
        }

        // Load quan hệ phòng với khu (ưu tiên từ cột phong_id - chỉ khi đã xác nhận)
        $sinhVien->load('phong.khu');
        $phongId = $sinhVien->phong_id;

        // Nếu chưa có phong_id, nhưng SV đã được gán vào slot -> suy ra phòng từ slot
        if (!$phongId) {
            $slot = Slot::with('phong.khu')->where('sinh_vien_id', $sinhVien->id)->first();
            if ($slot && $slot->phong) {
                $phongId = $slot->phong->id;
                // gắn tạm để view dùng luôn
                $sinhVien->setRelation('phong', $slot->phong);
            }
        }

        // Nếu có assignment chờ xác nhận, dùng phòng từ assignment để hiển thị
        // NHƯNG không gán vào sinh viên (để phân biệt đã xác nhận hay chưa)
        $displayPhong = null;
        if ($pendingRoomAssignment && $pendingRoomAssignment->phong) {
            $displayPhong = $pendingRoomAssignment->phong;
            // Cập nhật phongId để tính toán hóa đơn
            if (!$phongId) {
                $phongId = $pendingRoomAssignment->phong_id;
            }
        } elseif ($sinhVien->phong) {
            $displayPhong = $sinhVien->phong;
        }

        // Thống kê cơ bản
        // Đếm hóa đơn chưa thanh toán của sinh viên
        $hoaDonChuaThanhToan = 0;
        if ($phongId) {
            // Lấy tất cả hóa đơn của phòng
            $hoaDons = HoaDon::where('phong_id', $phongId)->get();

            foreach ($hoaDons as $hoaDon) {
                $hasUnpaidPayment = false;

                // Kiểm tra xem hóa đơn này có slot payment hoặc utilities payment không
                $hasSlotPayments = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)->exists();
                $hasUtilitiesPayments = HoaDonUtilitiesPayment::where('hoa_don_id', $hoaDon->id)->exists();

                if ($hasSlotPayments || $hasUtilitiesPayments) {
                    // Hệ thống mới: kiểm tra từng loại payment của sinh viên

                    // Kiểm tra slot payment của sinh viên
                    $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
                        ->where('sinh_vien_id', $sinhVien->id)
                        ->first();

                    if ($slotPayment) {
                        // Chỉ đếm là chưa thanh toán nếu: chưa thanh toán VÀ không phải đang chờ xác nhận
                        if ((!$slotPayment->da_thanh_toan || is_null($slotPayment->da_thanh_toan))
                            && $slotPayment->trang_thai !== HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN
                            && $slotPayment->trang_thai !== HoaDonSlotPayment::TRANG_THAI_DA_THANH_TOAN
                        ) {
                            $hasUnpaidPayment = true;
                        }
                    }

                    // Kiểm tra utilities payment của sinh viên
                    $utilitiesPayment = HoaDonUtilitiesPayment::where('hoa_don_id', $hoaDon->id)
                        ->where('sinh_vien_id', $sinhVien->id)
                        ->first();

                    if ($utilitiesPayment) {
                        // Chỉ đếm là chưa thanh toán nếu: chưa thanh toán VÀ không phải đang chờ xác nhận
                        if ((!$utilitiesPayment->da_thanh_toan || is_null($utilitiesPayment->da_thanh_toan))
                            && $utilitiesPayment->trang_thai !== HoaDonUtilitiesPayment::TRANG_THAI_CHO_XAC_NHAN
                            && $utilitiesPayment->trang_thai !== HoaDonUtilitiesPayment::TRANG_THAI_DA_THANH_TOAN
                        ) {
                            $hasUnpaidPayment = true;
                        }
                    }
                } else {
                    // Hệ thống cũ: kiểm tra da_thanh_toan của hóa đơn
                    if (!$hoaDon->da_thanh_toan || is_null($hoaDon->da_thanh_toan)) {
                        $hasUnpaidPayment = true;
                    }
                }

                if ($hasUnpaidPayment) {
                    $hoaDonChuaThanhToan++;
                }
            }
        }

        $stats = [
            'phong' => $displayPhong, // Hiển thị phòng từ assignment nếu chưa xác nhận, hoặc từ phong_id nếu đã xác nhận
            'so_su_co' => SuCo::where('sinh_vien_id', $sinhVien->id)->count(),
            'su_co_chua_xu_ly' => SuCo::where('sinh_vien_id', $sinhVien->id)
                // Đếm các sự cố chưa hoàn thành: Tiếp nhận hoặc Đang xử lý
                ->whereIn('trang_thai', ['Tiếp nhận', 'Đang xử lý'])
                ->count(),
            'hoa_don_chua_thanh_toan' => $hoaDonChuaThanhToan,
        ];

        // Sự cố gần đây
        $suCoGanDay = SuCo::where('sinh_vien_id', $sinhVien->id)
            ->with('phong')
            ->orderBy('ngay_gui', 'desc')
            ->limit(5)
            ->get();

        // Đánh dấu xem sinh viên đã xác nhận vào phòng chưa (có phong_id thực sự)
        $hasConfirmedRoom = !empty($sinhVien->phong_id);

        return view('client.dashboard', compact('user', 'sinhVien', 'stats', 'suCoGanDay', 'pendingRoomAssignment', 'hasConfirmedRoom'));
    }

    /**
     * Thông tin phòng của sinh viên
     */
    public function phong()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        // Nếu chưa có sinh viên, vẫn cho vào giao diện
        if (!$sinhVien) {
            $phong = null;
            return view('client.phong', compact('user', 'sinhVien', 'phong'));
        }

        // QUAN TRỌNG: Kiểm tra nếu sinh viên chưa có phòng được xác nhận (chưa thanh toán)
        // Chỉ cho vào phòng khi đã có phong_id (đã thanh toán và được gán vào phòng)
        if (empty($sinhVien->phong_id)) {
            // Kiểm tra xem có assignment đang chờ xác nhận không
            $pendingAssignment = RoomAssignment::where('sinh_vien_id', $sinhVien->id)
                ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
                ->whereNull('end_date')
                ->latest('start_date')
                ->first();

            if ($pendingAssignment) {
                return redirect()->route('client.dashboard')
                    ->with('warning', 'Bạn cần xác nhận và thanh toán tiền phòng để xem thông tin phòng. Vui lòng click vào nút "Xác nhận vào phòng" trên trang tổng quan.');
            }

            return redirect()->route('client.dashboard')
                ->with('warning', 'Bạn chưa được gán vào phòng. Vui lòng liên hệ ban quản lý.');
        }

        // Load quan hệ phòng với khu (nếu có)
        $sinhVien->load('phong.khu');
        $phong = $sinhVien->phong;

        // Nếu chưa có phong_id nhưng đã gán slot -> lấy phòng từ slot
        if (!$phong) {
            $slot = Slot::with('phong.khu')->where('sinh_vien_id', $sinhVien->id)->first();
            $phong = $slot?->phong;
        }

        // Bổ sung dữ liệu hiển thị: số người, tài sản phòng, tài sản cá nhân đã bàn giao
        $soNguoiTrongPhong = null;
        $danhSachSinhVien = collect([]);
        $taiSanPhong = collect([]);
        $taiSanCaNhan = collect([]);
        $slotSinhVien = null;

        if ($phong) {
            // Số người đang ở (đếm slots có sinh_vien_id)
            $soNguoiTrongPhong = $phong->usedSlots();

            // Danh sách sinh viên trong phòng (từ slots có sinh_vien_id)
            $danhSachSinhVien = Slot::with('sinhVien')
                ->where('phong_id', $phong->id)
                ->whereNotNull('sinh_vien_id')
                ->get()
                ->map(function ($slot) {
                    return $slot->sinhVien;
                })
                ->filter()
                ->values();

            // Tài sản chung của phòng (logic giống admin: chỉ lấy tài sản có available_quantity > 0)
            $roomAssets = TaiSan::with(['khoTaiSan', 'slots'])
                ->leftJoin('lich_bao_tri', function ($join) {
                    $join->on('tai_san.id', '=', 'lich_bao_tri.tai_san_id')
                        ->whereNull('lich_bao_tri.ngay_hoan_thanh'); // hoặc where trang_thai != 'Hoàn thành'
                })
                ->where('tai_san.phong_id', $phong->id)
                ->select('tai_san.*', 'lich_bao_tri.trang_thai as trang_thai_bao_tri')
                ->orderBy('ten_tai_san')
                ->get();


            $taiSanPhong = $roomAssets->map(function ($asset) {
                $assignedQuantity = $asset->slots->sum(function ($slotItem) {
                    return (int) ($slotItem->pivot->so_luong ?? 0);
                });
                $availableQuantity = max(0, (int) ($asset->so_luong ?? 0) - $assignedQuantity);

                $asset->setAttribute('assigned_slot_quantity', $assignedQuantity);
                $asset->setAttribute('available_quantity', $availableQuantity);

                return $asset;
            })->filter(function ($asset) {
                // Chỉ lấy tài sản chung (available_quantity > 0)
                return (int) $asset->available_quantity > 0;
            })->values();

            // Slot của sinh viên và tài sản đã bàn giao riêng
            $slotSinhVien = Slot::with(['taiSans.khoTaiSan'])
                ->where('phong_id', $phong->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();
            $taiSanCaNhan = collect([]);

            if ($slotSinhVien) {
                $taiSanCaNhan = TaiSan::with('khoTaiSan')
                    ->leftJoin('lich_bao_tri', function ($join) {
                        $join->on('tai_san.id', '=', 'lich_bao_tri.tai_san_id')
                            ->whereNull('lich_bao_tri.ngay_hoan_thanh'); // chỉ lấy đang bảo trì
                    })
                    ->whereIn('tai_san.id', $slotSinhVien->taiSans->pluck('id'))
                    ->select('tai_san.*', 'lich_bao_tri.trang_thai as trang_thai_bao_tri')
                    ->get();
            }
        }

        return view('client.phong', compact(
            'user',
            'sinhVien',
            'phong',
            'soNguoiTrongPhong',
            'danhSachSinhVien',
            'taiSanPhong',
            'taiSanCaNhan',
            'slotSinhVien'
        ));
    }

    /**
     * Hóa đơn điện nước & tiền phòng cho sinh viên
     */
    public function hoaDonIndex(Request $request)
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();
        $tab = $request->get('tab');

        if ($request->routeIs('client.hoadon.tienphong')) {
            $tab = 'tien-phong';
        } elseif ($request->routeIs('client.hoadon.diennuoc')) {
            $tab = 'dien-nuoc';
        }

        if (!in_array($tab, ['tien-phong', 'dien-nuoc'])) {
            $tab = 'dien-nuoc';
        }

        if (!$sinhVien) {
            return view('client.hoadon.index', compact('user', 'sinhVien'))
                ->with('phong', null)
                ->with('hoaDons', collect())
                ->with('selectedHoaDon', null)
                ->with('tab', $tab);
        }

        $sinhVien->load('phong.khu');
        $phong = $sinhVien->phong;

        if (!$phong) {
            $slot = Slot::with('phong.khu')->where('sinh_vien_id', $sinhVien->id)->first();
            $phong = $slot?->phong;
        }

        if (!$phong) {
            return view('client.hoadon.index', compact('user', 'sinhVien'))
                ->with('phong', null)
                ->with('hoaDons', collect())
                ->with('selectedHoaDon', null)
                ->with('tab', $tab)
                ->with('message', 'Bạn chưa được gán vào phòng nên không có hóa đơn.');
        }

        $hoaDons = HoaDon::with(['phong.khu', 'slotPayments', 'utilitiesPayments'])
            ->where('phong_id', $phong->id)
            ->where(function ($query) {
                $query->where('sent_to_client', true)
                    ->orWhere('sent_dien_nuoc_to_client', true);
            })
            ->orderByDesc('created_at')
            ->get();

        $hoaDons->each(function ($hoaDon) use ($sinhVien) {
            $so_dien = max(0, ($hoaDon->so_dien_moi ?? 0) - ($hoaDon->so_dien_cu ?? 0));
            $so_nuoc = max(0, ($hoaDon->so_nuoc_moi ?? 0) - ($hoaDon->so_nuoc_cu ?? 0));

            $hoaDon->san_luong_dien = $so_dien;
            $hoaDon->san_luong_nuoc = $so_nuoc;
            $hoaDon->tien_dien = $so_dien * ($hoaDon->don_gia_dien ?? 0);
            $hoaDon->tien_nuoc = $so_nuoc * ($hoaDon->don_gia_nuoc ?? 0);

            $this->enrichHoaDonWithPhongPricing($hoaDon);
            $this->attachSlotBreakdown($hoaDon);
            $this->initializeSlotPayments($hoaDon);
            $this->initializeUtilitiesPayments($hoaDon);
            $hoaDon->setRelation('utilitiesPayments', $hoaDon->utilitiesPayments()->get());

            $hoaDon->slotPaymentsMap = $hoaDon->slotPayments->keyBy('slot_label');
            $hoaDon->utilitiesPaymentsMap = $hoaDon->utilitiesPayments->keyBy('slot_label');

            $studentPayment = $hoaDon->slotPayments->firstWhere('sinh_vien_id', $sinhVien->id);
            if ($studentPayment) {
                $matchingBreakdown = collect($hoaDon->slot_breakdowns ?? [])
                    ->firstWhere('label', $studentPayment->slot_label);
                $hoaDon->student_payment = $studentPayment;
                $hoaDon->student_breakdown = $matchingBreakdown;
            }

            $studentUtilitiesPayment = $hoaDon->utilitiesPayments->firstWhere('sinh_vien_id', $sinhVien->id);
            if ($studentUtilitiesPayment) {
                $matchingUtilitiesBreakdown = collect($hoaDon->slot_breakdowns ?? [])
                    ->firstWhere('label', $studentUtilitiesPayment->slot_label);
                $hoaDon->student_utilities_payment = $studentUtilitiesPayment;
                $hoaDon->student_utilities_breakdown = $matchingUtilitiesBreakdown;
            }
        });

        $hoaDons = $hoaDons->filter(function ($hoaDon) use ($tab) {
            return $tab === 'tien-phong'
                ? $hoaDon->sent_to_client
                : $hoaDon->sent_dien_nuoc_to_client;
        })->values();

        $selectedHoaDonId = $request->get('hoa_don_id');
        $selectedHoaDon = $hoaDons->firstWhere('id', $selectedHoaDonId) ?? $hoaDons->first();

        return view('client.hoadon.index', compact(
            'user',
            'sinhVien',
            'phong',
            'hoaDons',
            'selectedHoaDon',
            'tab'
        ));
    }
    // Lịch sử tiền phòng
    public function lichSuTienPhong()
    {
        $sinhVienId = Auth::id();

        // Lấy các khoản tiền phòng mà sinh viên này đã thanh toán
        $hoaDons = HoaDonUtilitiesPayment::where('sinh_vien_id', $sinhVienId)
            ->where('da_thanh_toan', true)
            ->whereNull('tien_dien')   // loại bỏ điện
            ->whereNull('tien_nuoc')   // loại bỏ nước
            ->orderByDesc('ngay_thanh_toan')
            ->get();

        return view('client.hoadon.lichsu_tienphong', compact('hoaDons'));
    }


    // Lịch sử điện nước
    public function lichSuDienNuoc()
    {
        $sinhVienId = Auth::id();

        $hoaDons = HoaDonUtilitiesPayment::where('sinh_vien_id', $sinhVienId)
            ->where('da_thanh_toan', true)
            ->where(function ($query) {
                $query->whereNotNull('tien_dien')
                    ->orWhereNotNull('tien_nuoc');
            })
            ->orderByDesc('ngay_thanh_toan')
            ->get();

        return view('client.hoadon.lichsu_diennuoc', compact('hoaDons'));
    }

    /**
     * Thông tin cá nhân
     */
    public function profile()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        $violations = $sinhVien
            ? $sinhVien->violations()->with('type')->latest('occurred_at')->get()
            : collect();

        // Nếu chưa có sinh viên, vẫn cho vào giao diện
        return view('client.profile', compact('user', 'sinhVien', 'violations'));
    }

    /**
     * Cho phép sinh viên xem lại hồ sơ trong bước xác nhận
     * (không yêu cầu middleware student để tránh redirect vòng lặp)
     */
    public function previewProfile()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();
        $violations = $sinhVien
            ? $sinhVien->violations()->with('type')->latest('occurred_at')->get()
            : collect();

        if (!$sinhVien) {
            return redirect()
                ->route('client.confirmation.show')
                ->with('warning', 'Không tìm thấy hồ sơ sinh viên gắn với tài khoản này.');
        }

        return view('client.profile', compact('user', 'sinhVien', 'violations'));
    }

    /**
     * Sinh viên gửi thanh toán cho vi phạm
     */
    public function payViolation(Request $request, Violation $violation)
    {
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien || $violation->sinh_vien_id !== $sinhVien->id) {
            abort(403, 'Bạn không thể thanh toán vi phạm này.');
        }

        if ($violation->status === 'resolved') {
            $payload = [
                'success' => false,
                'message' => 'Vi phạm này đã được xử lý.',
            ];
            return $request->expectsJson()
                ? response()->json($payload, 422)
                : back()->with('warning', $payload['message']);
        }

        $data = $request->validate([
            'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'ghi_chu' => 'nullable|string|max:2000',
            'anh_chuyen_khoan' => 'nullable|image|max:4096',
        ]);

        $violation->client_payment_method = $data['hinh_thuc_thanh_toan'];
        $violation->client_payment_note = $data['ghi_chu'] ?? null;
        $violation->client_paid_at = now();
        $violation->status = 'resolved';

        if ($data['hinh_thuc_thanh_toan'] === 'chuyen_khoan') {
            if ($request->hasFile('anh_chuyen_khoan')) {
                if ($violation->client_transfer_image_path) {
                    Storage::disk('public')->delete($violation->client_transfer_image_path);
                }
                $violation->client_transfer_image_path = $request->file('anh_chuyen_khoan')
                    ->store('violation_payments', 'public');
            } elseif (!$violation->client_transfer_image_path) {
                $payload = [
                    'success' => false,
                    'message' => 'Vui lòng tải lên ảnh chứng từ chuyển khoản.',
                ];
                return $request->expectsJson()
                    ? response()->json($payload, 422)
                    : back()->with('warning', $payload['message']);
            }
        } else {
            if ($violation->client_transfer_image_path) {
                Storage::disk('public')->delete($violation->client_transfer_image_path);
            }
            $violation->client_transfer_image_path = null;
        }

        $violation->save();

        $payload = [
            'success' => true,
            'message' => 'Đã ghi nhận thanh toán, vi phạm sẽ hiển thị là "Đã xử lý".',
            'violation' => [
                'id' => $violation->id,
                'status' => $violation->status,
                'client_paid_at' => optional($violation->client_paid_at)->format('d/m/Y H:i'),
            ],
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : back()->with('success', $payload['message']);
    }

    /**
     * Trang xác nhận hồ sơ sau khi được admin duyệt
     */
    public function showConfirmation()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien) {
            return redirect()->route('public.apply')
                ->with('warning', 'Bạn chưa gửi hồ sơ đăng ký ký túc xá.');
        }

        if ($sinhVien->isPendingApproval()) {
            return redirect()->route('public.home')
                ->with('warning', 'Hồ sơ của bạn vẫn đang trong trạng thái chờ ban quản lý duyệt.');
        }

        if ($sinhVien->isApproved()) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Hồ sơ đã được xác nhận trước đó.');
        }

        return view('client.confirmation', compact('user', 'sinhVien'));
    }

    /**
     * Sinh viên xác nhận hồ sơ -> chuyển sang trạng thái Đã duyệt
     */
    public function confirmApproval(Request $request)
    {
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien) {
            return redirect()->route('public.apply')
                ->with('warning', 'Bạn chưa gửi hồ sơ đăng ký ký túc xá.');
        }

        if (!$sinhVien->isPendingConfirmation()) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Hồ sơ của bạn không ở trạng thái cần xác nhận.');
        }

        $sinhVien->trang_thai_ho_so = SinhVien::STATUS_APPROVED;
        $sinhVien->save();

        return redirect()->route('client.dashboard')
            ->with('success', 'Đã xác nhận hồ sơ thành công. Bạn có thể sử dụng đầy đủ các chức năng dành cho sinh viên.');
    }
    public function baoHong(Request $request)
    {
        // Validate input
        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'mo_ta' => 'required|string',
            'hinh_anh_truoc' => 'nullable|image|max:4096'
        ]);

        // Lấy sinh viên hiện tại
        $sinhVien = $this->getSinhVien();
        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên.');
        }

        // Lấy phòng của sinh viên
        $phongId = $sinhVien->phong_id ?? Slot::where('sinh_vien_id', $sinhVien->id)->value('phong_id');
        if (!$phongId) {
            return back()->with('error', 'Bạn chưa ở trong phòng nào.');
        }

        // Lấy tài sản
        $taiSan = TaiSan::find($request->tai_san_id);
        if (!$taiSan) {
            return back()->with('error', 'Không tìm thấy tài sản.');
        }

        // Kiểm tra quyền truy cập: tài sản phải thuộc phòng của sinh viên
        if ($taiSan->phong_id != $phongId) {
            return back()->with('error', 'Tài sản này không thuộc phòng của bạn.');
        }

        // Kiểm tra trạng thái tài sản
        if (in_array($taiSan->tinh_trang_hien_tai, ['Đang bảo trì', 'Đã báo hỏng'])) {
            return back()->with('error', 'Tài sản này đang trong quá trình xử lý. Vui lòng chờ hoàn thành trước khi báo hỏng mới.');
        }

        // Xử lý upload ảnh (giữ nguyên đường dẫn hiện tại)
        $imagePath = null;
        if ($request->hasFile('hinh_anh_truoc')) {
            $file = $request->file('hinh_anh_truoc');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $filename);
            $imagePath = $filename;
        }

        // Kiểm tra nếu có lịch "Từ chối tiếp nhận" gần nhất
        $lichTuChoi = LichBaoTri::where('tai_san_id', $request->tai_san_id)
            ->where('trang_thai', 'Từ chối tiếp nhận')
            ->latest()
            ->first();

        if ($lichTuChoi) {
            // Cập nhật lại lịch từ chối thành "Đang lên lịch"
            $lichTuChoi->update([
                'trang_thai'      => 'Đang lên lịch',
                'mo_ta'           => $request->mo_ta,
                'hinh_anh_truoc'  => $imagePath,
                'ngay_bao_tri'    => now()->toDateString(),
            ]);

            // Cập nhật trạng thái tài sản
            $taiSan->update([
                'tinh_trang_hien_tai' => 'Đã báo hỏng'
            ]);

            return back()->with('success', 'Đã gửi lại báo hỏng thành công!');
        }

        // Kiểm tra các lịch đang xử lý thực sự (Đang lên lịch, Chờ bảo trì, Đang bảo trì)
        $existingBaoTri = LichBaoTri::where('tai_san_id', $request->tai_san_id)
            ->whereIn('trang_thai', ['Đang lên lịch', 'Chờ bảo trì', 'Đang bảo trì'])
            ->exists();

        if ($existingBaoTri) {
            return back()->with('error', 'Tài sản này đang có bảo trì chưa hoàn thành. Vui lòng chờ hoàn thành bảo trì trước khi báo hỏng mới.');
        }

        // Nếu không có lịch từ chối hoặc lịch đang xử lý → tạo lịch mới
        LichBaoTri::create([
            'tai_san_id'      => $request->tai_san_id,
            'kho_tai_san_id'  => $taiSan->kho_tai_san_id,
            'location_type'   => 'phong',
            'location_id'     => $phongId,
            'mo_ta'           => $request->mo_ta,
            'hinh_anh_truoc'  => $imagePath,
            'ngay_bao_tri'    => now()->toDateString(),
            'trang_thai'      => 'Đang lên lịch'
        ]);

        // Cập nhật trạng thái tài sản
        $taiSan->update([
            'tinh_trang_hien_tai' => 'Đã báo hỏng'
        ]);

        return back()->with('success', 'Đã gửi báo hỏng thành công!');
    }



    public function suCoIndex()
    {
        $sinhVien = $this->getSinhVien();
        $phong = $sinhVien?->phong ?? $sinhVien?->slot?->phong ?? null;

        $dsSuCo = $sinhVien
            ? SuCo::where('sinh_vien_id', $sinhVien->id)
            ->orderBy('ngay_gui', 'desc')
            ->paginate(10)
            : collect();

        // Thêm xử lý URL ảnh ngay trong controller
        $dsSuCo->transform(function ($sc) {
            // Ảnh sinh viên upload lúc báo sự cố
            $sc->anh_url = ($sc->anh && file_exists(storage_path('app/public/' . $sc->anh)))
                ? asset('storage/' . $sc->anh)
                : null;

            // Ảnh admin upload sau xử lý
            $sc->anh_sau_url = ($sc->anh_sau && file_exists(storage_path('app/public/' . $sc->anh_sau)))
                ? asset('storage/' . $sc->anh_sau)
                : null;

            // Nếu cả 2 đều null → fallback ảnh dummy
            $sc->display_anh = $sc->anh_sau_url ?? $sc->anh_url ?? 'https://dummyimage.com/150x150/eff3f9/9aa8b8&text=IMG';

            return $sc;
        });

        return view('client.suco', compact('sinhVien', 'phong', 'dsSuCo'));
    }


    public function suCoStore(Request $request)
    {
        $sinhVien = $this->getSinhVien();
        $phong = $sinhVien?->phong ?? $sinhVien?->slot?->phong ?? null;

        if (!$sinhVien || !$phong) {
            return back()->withErrors(['error' => 'Không tìm thấy thông tin phòng để báo sự cố']);
        }

        // Validate dữ liệu
        $request->validate([
            'mo_ta' => 'required|string|max:1000',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $suCo = new SuCo();
        $suCo->sinh_vien_id = $sinhVien->id;
        $suCo->phong_id = $phong->id;
        $suCo->mo_ta = $request->mo_ta;

        // Upload ảnh giống admin
        if ($request->hasFile('anh')) {
            $uploadPath = public_path('uploads/suco');
            if (!\File::exists($uploadPath)) {
                \File::makeDirectory($uploadPath, 0755, true);
            }
            $file = $request->file('anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $suCo->anh = 'uploads/suco/' . $fileName; // Lưu đường dẫn giống admin
        }

        $suCo->trang_thai = 'Tiếp nhận';
        $suCo->ngay_gui = now();
        $suCo->nguoi_tao = 'sinh_vien'; // đánh dấu người tạo
        $suCo->save();

        return redirect()->route('client.suco.index')->with('success', 'Báo sự cố thành công!');
    }

    /**
     * Xem lịch bảo trì tài sản của phòng sinh viên
     */
    public function lichBaoTriIndex(Request $request)
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        // Nếu chưa có sinh viên
        if (!$sinhVien) {
            return view('client.lichbaotri', compact('user', 'sinhVien'))
                ->with('phong', null)
                ->with('dangXuLy', collect())
                ->with('daHoanThanh', collect());
        }

        // Lấy phòng của sinh viên
        $sinhVien->load('phong.khu');
        $phong = $sinhVien->phong;

        // Nếu chưa có phong_id nhưng đã gán slot -> lấy phòng từ slot
        if (!$phong) {
            $slot = Slot::with('phong.khu')->where('sinh_vien_id', $sinhVien->id)->first();
            $phong = $slot?->phong;
        }

        // Nếu chưa có phòng
        if (!$phong) {
            return view('client.lichbaotri', compact('user', 'sinhVien', 'phong'))
                ->with('dangXuLy', collect())
                ->with('daHoanThanh', collect());
        }

        // Lấy lịch bảo trì của phòng (location_type = 'phong' và location_id = phong_id)
        $baseQuery = LichBaoTri::with(['taiSan.khoTaiSan', 'taiSan.phong', 'khoTaiSan'])
            ->where('location_type', 'phong')
            ->where('location_id', $phong->id);

        // Tính tổng số cho cả 2 tab (để hiển thị badge)
        $dangXuLyCount = (clone $baseQuery)
            ->where('trang_thai', '!=', 'Hoàn thành')
            ->count();
        $daHoanThanhCount = (clone $baseQuery)
            ->where('trang_thai', 'Hoàn thành')
            ->count();

        // Lọc theo tab
        $tab = $request->get('tab', 'dang-xu-ly');

        if ($tab === 'da-hoan-thanh') {
            // Tab "Đã hoàn thành": chỉ lấy các bảo trì đã hoàn thành
            $daHoanThanh = (clone $baseQuery)
                ->where('trang_thai', 'Hoàn thành')
                ->orderBy('ngay_hoan_thanh', 'desc')
                ->paginate(10, ['*'], 'page')
                ->appends($request->query());

            $dangXuLy = null;
        } else {
            // Tab "Đang xử lý": lấy các trạng thái chưa hoàn thành
            $dangXuLy = (clone $baseQuery)
                ->where('trang_thai', '!=', 'Hoàn thành')
                ->orderBy('ngay_bao_tri', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page')
                ->appends($request->query());

            $daHoanThanh = null;
        }

        return view('client.lichbaotri', compact(
            'user',
            'sinhVien',
            'phong',
            'dangXuLy',
            'daHoanThanh',
            'tab',
            'dangXuLyCount',
            'daHoanThanhCount'
        ));
    }

    public function su_co_thanhtoan(Request $request, $id)
    {
        $sinhVien = Auth::user()?->sinhVien;
        if (!$sinhVien) {
            return response()->json([
                'success' => false,
                'message' => 'Sinh viên không tồn tại.'
            ]);
        }

        $suCo = SuCo::where('id', $id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->first();

        if (!$suCo) {
            return response()->json([
                'success' => false,
                'message' => 'Sự cố không tồn tại hoặc không thuộc bạn.'
            ]);
        }

        if ($suCo->is_paid) {
            return response()->json([
                'success' => false,
                'message' => 'Sự cố này đã được thanh toán.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'hinh_thuc_thanh_toan' => 'required|string|in:tien_mat,chuyen_khoan',
            'ghi_chu_thanh_toan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $suCo->payment_method = $request->hinh_thuc_thanh_toan;
        $suCo->payment_note = $request->ghi_chu_thanh_toan;
        $suCo->is_paid = 1;
        $suCo->ngay_thanh_toan = now();
        $suCo->save();

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công!'
        ]);
    }

    /**
     * Hiển thị trang xác nhận phòng
     */
    public function showRoomConfirmation()
    {
        $user = Auth::user();
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien) {
            return redirect()->route('public.apply')
                ->with('warning', 'Bạn chưa gửi hồ sơ đăng ký ký túc xá.');
        }

        // Tìm assignment đang chờ xác nhận
        $assignment = RoomAssignment::where('sinh_vien_id', $sinhVien->id)
            ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
            ->whereNull('end_date')
            ->with(['phong' => function ($q) {
                $q->with('khu');
            }])
            ->latest('start_date')
            ->first();

        if (!$assignment) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Bạn không có yêu cầu gán phòng nào đang chờ xác nhận.');
        }

        // QUAN TRỌNG: Kiểm tra xem sinh viên đã thanh toán chưa
        // Nếu đã có phong_id (đã thanh toán), redirect về dashboard
        if (!empty($sinhVien->phong_id) && $sinhVien->phong_id == $assignment->phong_id) {
            return redirect()->route('client.dashboard')
                ->with('success', 'Bạn đã thanh toán và được gán vào phòng thành công!');
        }

        // Tìm hóa đơn và slot payment của sinh viên
        $currentMonth = \Carbon\Carbon::now()->format('m/Y');
        $hoaDon = HoaDon::where('phong_id', $assignment->phong_id)
            ->where('thang', $currentMonth)
            ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
            ->first();

        $slotPayment = null;
        if ($hoaDon) {
            $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();
        }

        // Nếu không tìm thấy slotPayment, tạo mới (có thể do gán slot trực tiếp)
        if (!$slotPayment && $hoaDon) {
            // Tìm slot của sinh viên trong phòng này
            $slot = \App\Models\Slot::where('phong_id', $assignment->phong_id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();

            $slotPayment = HoaDonSlotPayment::create([
                'hoa_don_id' => $hoaDon->id,
                'slot_id' => $slot ? $slot->id : null,
                'slot_label' => 'Chờ xác nhận - ' . $sinhVien->ho_ten,
                'sinh_vien_id' => $sinhVien->id,
                'sinh_vien_ten' => $sinhVien->ho_ten,
                'trang_thai' => HoaDonSlotPayment::TRANG_THAI_CHUA_THANH_TOAN,
                'da_thanh_toan' => false,
            ]);
        }

        return view('client.room_confirmation', compact('user', 'sinhVien', 'assignment', 'hoaDon', 'slotPayment'));
    }

    /**
     * Sinh viên xác nhận vào phòng và thanh toán
     */
    public function confirmRoomAssignment(Request $request)
    {
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien) {
            return redirect()->route('public.apply')
                ->with('warning', 'Bạn chưa gửi hồ sơ đăng ký ký túc xá.');
        }

        // Tìm assignment đang chờ xác nhận
        $assignment = RoomAssignment::where('sinh_vien_id', $sinhVien->id)
            ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
            ->whereNull('end_date')
            ->with('phong')
            ->latest('start_date')
            ->first();

        if (!$assignment) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Không tìm thấy yêu cầu gán phòng cần xác nhận.');
        }

        // Kiểm tra thanh toán tiền phòng
        $currentMonth = \Carbon\Carbon::now()->format('m/Y');
        $hoaDon = HoaDon::where('phong_id', $assignment->phong_id)
            ->where('thang', $currentMonth)
            ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
            ->first();

        if (!$hoaDon) {
            return redirect()->back()
                ->with('error', 'Không tìm thấy hóa đơn tiền phòng. Vui lòng liên hệ ban quản lý.');
        }

        $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
            ->where('sinh_vien_id', $sinhVien->id)
            ->first();

        if (!$slotPayment) {
            return redirect()->back()
                ->with('error', 'Không tìm thấy thông tin thanh toán. Vui lòng liên hệ ban quản lý.');
        }

        // Validate thanh toán
        $request->validate([
            'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'ghi_chu' => 'nullable|string|max:500',
            'anh_chuyen_khoan' => 'nullable|image|max:4096',
            'xac_nhan_tinh_trang_phong' => 'required|accepted',
        ], [
            'xac_nhan_tinh_trang_phong.required' => 'Vui lòng xác nhận tình trạng phòng và cơ sở vật chất phòng.',
            'xac_nhan_tinh_trang_phong.accepted' => 'Vui lòng xác nhận tình trạng phòng và cơ sở vật chất phòng.',
        ]);

        // Xử lý thanh toán và xác nhận vào phòng
        return DB::transaction(function () use ($sinhVien, $assignment, $slotPayment, $hoaDon, $request) {
            // Xử lý thanh toán
            $slotPayment->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
            $slotPayment->client_ghi_chu = $request->ghi_chu ?? '';
            $slotPayment->client_requested_at = now();

            if ($request->hasFile('anh_chuyen_khoan')) {
                $storedPath = $request->file('anh_chuyen_khoan')->store('slot-payments', 'public');
                $slotPayment->client_transfer_image_path = $storedPath;
            }

            // Nếu thanh toán bằng tiền mặt, tự động xác nhận thanh toán ngay và gán vào phòng
            if ($request->hinh_thuc_thanh_toan === 'tien_mat') {
                $slotPayment->da_thanh_toan = true;
                $slotPayment->trang_thai = HoaDonSlotPayment::TRANG_THAI_DA_THANH_TOAN;
                $slotPayment->ngay_thanh_toan = now();
                $slotPayment->xac_nhan_boi = auth()->id();
                $slotPayment->saveOrFail();

                // Xác nhận vào phòng ngay khi thanh toán tiền mặt
                $sinhVien->phong_id = $assignment->phong_id;
                $sinhVien->saveOrFail();

                // Cập nhật trạng thái assignment
                $assignment->trang_thai = RoomAssignment::STATUS_CONFIRMED;
                $assignment->saveOrFail();
            } else {
                // Chuyển khoản: chỉ tạo yêu cầu thanh toán, CHƯA gán vào phòng
                // Sinh viên phải chờ admin xác nhận thanh toán mới được vào phòng
                $slotPayment->trang_thai = HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN;
                $slotPayment->da_thanh_toan = false; // Chưa thanh toán cho đến khi admin xác nhận
                $slotPayment->saveOrFail();

                // KHÔNG gán vào phòng, giữ nguyên trạng thái assignment là PENDING_CONFIRMATION
                // Chỉ khi admin xác nhận thanh toán trong PaymentConfirmationController thì mới gán vào phòng

                // Cập nhật hóa đơn (không cần kiểm tra tất cả slot đã thanh toán vì chưa thanh toán)
                return redirect()->route('client.dashboard')
                    ->with('success', 'Đã gửi yêu cầu thanh toán chuyển khoản. Vui lòng chờ ban quản lý xác nhận. Sau khi được xác nhận, bạn sẽ được gán vào phòng.');
            }

            // Chỉ xử lý slot và cập nhật phòng nếu đã thanh toán tiền mặt (đã vào phòng)
            if ($request->hinh_thuc_thanh_toan === 'tien_mat') {
                // Tìm hoặc tạo slot cho sinh viên (nếu chưa có)
                $slot = Slot::where('phong_id', $assignment->phong_id)
                    ->where('sinh_vien_id', $sinhVien->id)
                    ->first();

                if (!$slot) {
                    // Tìm slot trống trong phòng
                    $emptySlot = Slot::where('phong_id', $assignment->phong_id)
                        ->whereNull('sinh_vien_id')
                        ->first();

                    if ($emptySlot) {
                        $emptySlot->sinh_vien_id = $sinhVien->id;
                        $emptySlot->saveOrFail();
                        $slotPayment->slot_id = $emptySlot->id;
                        $slotPayment->saveOrFail();
                    }
                } else {
                    $slotPayment->slot_id = $slot->id;
                    $slotPayment->saveOrFail();
                }

                // Cập nhật trạng thái phòng
                $phong = $assignment->phong;
                if ($phong && method_exists($phong, 'updateStatusBasedOnCapacity')) {
                    $phong->updateStatusBasedOnCapacity();
                }

                // Cập nhật hóa đơn nếu tất cả slot đã thanh toán
                $totalSlots = $hoaDon->slotPayments()->count();
                $paidSlots = $hoaDon->slotPayments()->where('da_thanh_toan', true)->count();

                if ($paidSlots >= $totalSlots && $totalSlots > 0) {
                    $hoaDon->trang_thai = 'Đã thanh toán';
                    $hoaDon->da_thanh_toan = true;
                    if (!$hoaDon->ngay_thanh_toan) {
                        $hoaDon->ngay_thanh_toan = now();
                    }
                    $hoaDon->saveOrFail();
                }

                return redirect()->route('client.dashboard')
                    ->with('success', 'Đã xác nhận vào phòng và thanh toán thành công!');
            }
        });
    }

    /**
     * Sinh viên từ chối phòng
     */
    public function rejectRoomAssignment(Request $request)
    {
        $sinhVien = $this->getSinhVien();

        if (!$sinhVien) {
            return redirect()->route('public.apply')
                ->with('warning', 'Bạn chưa gửi hồ sơ đăng ký ký túc xá.');
        }

        // Tìm assignment đang chờ xác nhận
        $assignment = RoomAssignment::where('sinh_vien_id', $sinhVien->id)
            ->where('trang_thai', RoomAssignment::STATUS_PENDING_CONFIRMATION)
            ->whereNull('end_date')
            ->with('phong.khu')
            ->latest('start_date')
            ->first();

        if (!$assignment) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Không tìm thấy yêu cầu gán phòng cần từ chối.');
        }

        // QUAN TRỌNG: Không cho phép từ chối nếu đã thanh toán hoặc đã xác nhận
        $currentMonth = \Carbon\Carbon::now()->format('m/Y');
        $hoaDon = HoaDon::where('phong_id', $assignment->phong_id)
            ->where('thang', $currentMonth)
            ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
            ->first();

        if ($hoaDon) {
            $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();

            // Nếu đã thanh toán hoặc đang chờ xác nhận (đã submit), không cho phép từ chối
            if ($slotPayment && ($slotPayment->da_thanh_toan || $slotPayment->trang_thai === HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN)) {
                return redirect()->route('client.dashboard')
                    ->with('error', 'Không thể từ chối phòng sau khi đã thanh toán hoặc đã xác nhận.');
            }
        }

        // Nếu đã có phong_id (đã xác nhận và thanh toán), không cho phép từ chối
        if ($sinhVien->phong_id && $sinhVien->phong_id == $assignment->phong_id) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Không thể từ chối phòng sau khi đã xác nhận và thanh toán.');
        }

        return DB::transaction(function () use ($assignment, $sinhVien) {
            // QUAN TRỌNG: Đảm bảo phong_id của sinh viên = null khi từ chối
            // Sinh viên sẽ chờ admin gán phòng khác
            if ($sinhVien->phong_id == $assignment->phong_id) {
                $sinhVien->phong_id = null;
                $sinhVien->saveOrFail();
            }

            // Cập nhật trạng thái assignment
            $assignment->trang_thai = RoomAssignment::STATUS_REJECTED;
            $assignment->end_date = now()->toDateString();
            $assignment->saveOrFail();

            // Xóa slot payment nếu có
            $currentMonth = \Carbon\Carbon::now()->format('m/Y');
            $hoaDon = HoaDon::where('phong_id', $assignment->phong_id)
                ->where('thang', $currentMonth)
                ->where('invoice_type', HoaDon::LOAI_TIEN_PHONG)
                ->first();

            if ($hoaDon) {
                $slotPayment = HoaDonSlotPayment::where('hoa_don_id', $hoaDon->id)
                    ->where('sinh_vien_id', $sinhVien->id)
                    ->first();

                if ($slotPayment) {
                    $slotPayment->delete();

                    // Cập nhật lại hóa đơn
                    $remainingPayments = $hoaDon->slotPayments()->count();
                    if ($remainingPayments > 0) {
                        $hoaDon->slot_billing_count = $remainingPayments;
                        $hoaDon->tien_phong_slot = $hoaDon->slot_unit_price * $remainingPayments;
                        $hoaDon->saveOrFail();
                    } else {
                        $hoaDon->delete();
                    }
                }
            }

            // Gỡ sinh viên khỏi slot nếu có
            $slot = Slot::where('phong_id', $assignment->phong_id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();

            if ($slot) {
                $slot->sinh_vien_id = null;
                $slot->saveOrFail();
            }

            // Cập nhật trạng thái phòng
            $phong = $assignment->phong;
            if ($phong && method_exists($phong, 'updateStatusBasedOnCapacity')) {
                $phong->updateStatusBasedOnCapacity();
            }

            // Gửi thông báo cho admin
            try {
                ThongBaoPhongSv::create([
                    'sinh_vien_id' => $sinhVien->id,
                    'phong_id' => $assignment->phong_id,
                    'noi_dung' => "Sinh viên {$sinhVien->ho_ten} (Mã SV: {$sinhVien->ma_sinh_vien}) đã từ chối phòng " . ($assignment->phong->ten_phong ?? 'N/A') . ($assignment->phong->khu ? " - Khu {$assignment->phong->khu->ten_khu}" : ''),
                ]);
            } catch (\Exception $e) {
                \Log::error('Lỗi tạo thông báo từ chối phòng: ' . $e->getMessage());
            }

            return redirect()->route('client.dashboard')
                ->with('info', 'Đã từ chối phòng. Bạn sẽ chờ ban quản lý gán phòng khác.');
        });
    }
    public function thanhToanBaoTri($id)
{
    $hoaDon = HoaDonBaoTri::with('lichBaoTri')->find($id);

    if (!$hoaDon) {
        return back()->with('error', 'Không tìm thấy hóa đơn bảo trì.');
    }

    // 🔥 Cập nhật hóa đơn theo đúng tên cột bạn đã dùng trong update()
    $hoaDon->trang_thai_thanh_toan = 'Đã thanh toán';
    $hoaDon->phuong_thuc_thanh_toan = 'Sinh viên tự thanh toán'; // hoặc null tùy bạn muốn để gì
    $hoaDon->ghi_chu = null; // hoặc ghi chú gì đó nếu cần
    $hoaDon->save();

    // 🔥 Cập nhật lịch bảo trì về "Hoàn thành"
    if ($hoaDon->lichBaoTri) {
        $lich = $hoaDon->lichBaoTri;
        $lich->trang_thai = 'Hoàn thành';
        $lich->ngay_hoan_thanh = now();
        $lich->save();
    }

    return back()->with('success', 'Thanh toán thành công!');
}

}
