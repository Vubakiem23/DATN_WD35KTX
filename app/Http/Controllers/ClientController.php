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
use App\Models\Slot;
use App\Models\TaiSan;
use Illuminate\Support\Facades\Validator;
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

        // Load quan hệ phòng với khu (ưu tiên từ cột phong_id)
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
            'phong' => $sinhVien->phong ?? null,
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

        return view('client.dashboard', compact('user', 'sinhVien', 'stats', 'suCoGanDay'));
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

        // Nếu chưa có sinh viên, vẫn cho vào giao diện
        return view('client.profile', compact('user', 'sinhVien'));
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
}
