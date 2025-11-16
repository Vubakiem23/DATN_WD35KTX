<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\SuCo;
use App\Models\HoaDon;
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
                ->where(function($q) {
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
        $taiSanPhong = collect([]);
        $taiSanCaNhan = collect([]);
        $slotSinhVien = null;

        if ($phong) {
            // Số người đang ở (đếm slots có sinh_vien_id)
            $soNguoiTrongPhong = $phong->usedSlots();

            // Tài sản của phòng
            $taiSanPhong = TaiSan::with('khoTaiSan')
                ->where('phong_id', $phong->id)
                ->orderBy('ten_tai_san')
                ->get();

            // Slot của sinh viên và tài sản đã bàn giao riêng
            $slotSinhVien = Slot::with(['taiSans.khoTaiSan'])
                ->where('phong_id', $phong->id)
                ->where('sinh_vien_id', $sinhVien->id)
                ->first();
            $taiSanCaNhan = $slotSinhVien?->taiSans ?? collect([]);
        }
        
        return view('client.phong', compact(
            'user',
            'sinhVien',
            'phong',
            'soNguoiTrongPhong',
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


            // Sự cố của sinh viên 
            

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
    $dsSuCo->transform(function($sc) {
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
        $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);
        $suCo->anh = 'uploads/suco/' . $fileName; // Lưu đường dẫn giống admin
    }

    $suCo->trang_thai = 'Tiếp nhận';
    $suCo->ngay_gui = now();
    $suCo->nguoi_tao = 'sinh_vien'; // đánh dấu người tạo
    $suCo->save();

    return redirect()->route('client.suco.index')->with('success', 'Báo sự cố thành công!');
}




}
