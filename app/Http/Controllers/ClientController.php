<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\LichBaoTri;
use App\Models\Slot;
use App\Models\TaiSan;

class ClientController extends Controller
{
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
        $stats = [
            'phong' => $sinhVien->phong ?? null,
            'so_su_co' => SuCo::where('sinh_vien_id', $sinhVien->id)->count(),
            'su_co_chua_xu_ly' => SuCo::where('sinh_vien_id', $sinhVien->id)
                // Đếm các sự cố chưa hoàn thành: Tiếp nhận hoặc Đang xử lý
                ->whereIn('trang_thai', ['Tiếp nhận', 'Đang xử lý'])
                ->count(),
            'hoa_don_chua_thanh_toan' => $phongId ? HoaDon::where('phong_id', $phongId)
                ->where(function ($q) {
                    $q->where('da_thanh_toan', false)
                        ->orWhereNull('da_thanh_toan');
                })
                ->count() : 0,
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
        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'mo_ta' => 'required|string',
            'hinh_anh_truoc' => 'nullable|image|max:4096'
        ]);

        $sinhVien = $this->getSinhVien();
        if (!$sinhVien) {
            return back()->with('error', 'Không tìm thấy thông tin sinh viên.');
        }

        $phongId = $sinhVien->phong_id ?? Slot::where('sinh_vien_id', $sinhVien->id)->value('phong_id');
        if (!$phongId) {
            return back()->with('error', 'Bạn chưa ở trong phòng nào.');
        }

        if ($request->hasFile('hinh_anh_truoc')) {
            $file = $request->file('hinh_anh_truoc');
            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/lichbaotri'), $filename);

            // Lưu giống admin
            $imagePath = $filename;
        } else {
            $imagePath = null;
        }




        LichBaoTri::create([
            'tai_san_id'   => $request->tai_san_id,
            'phong_id'     => $phongId,
            'mo_ta'        => $request->mo_ta,
            'hinh_anh_truoc'     => $imagePath,
            'ngay_bao_tri' => now()->toDateString(),
            'trang_thai'   => 'Đang bảo trì'
        ]);

        return back()->with('success', 'Đã gửi báo hỏng thành công!');
    }
}
