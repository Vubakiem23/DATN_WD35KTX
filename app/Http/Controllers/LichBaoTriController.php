<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LichBaoTri;
use App\Models\TaiSan;
use App\Models\KhoTaiSan;
use App\Models\HoaDonBaoTri;

use Illuminate\Support\Facades\DB;

class LichBaoTriController extends Controller
{
    /** 🧭 Hiển thị danh sách lịch bảo trì */
    /** 🧭 Hiển thị danh sách lịch bảo trì */
public function index(Request $request)
{
    $today = now()->toDateString();

    // ✅ Không ép trạng thái "Chờ thanh toán" thành "Hoàn thành"
    DB::table('lich_bao_tri')
        ->whereNotNull('ngay_hoan_thanh')
        ->whereNotIn('trang_thai', ['Hoàn thành', 'Từ chối tiếp nhận', 'Chờ thanh toán'])
        ->update(['trang_thai' => 'Hoàn thành', 'updated_at' => now()]);

    DB::table('lich_bao_tri')
        ->whereNull('ngay_hoan_thanh')
        ->whereDate('ngay_bao_tri', '>', $today)
        ->whereNotIn('trang_thai', ['Chờ bảo trì', 'Đang lên lịch', 'Từ chối tiếp nhận', 'Chờ thanh toán'])
        ->update(['trang_thai' => 'Chờ bảo trì', 'updated_at' => now()]);

    DB::table('lich_bao_tri')
        ->whereNull('ngay_hoan_thanh')
        ->whereDate('ngay_bao_tri', '<=', $today)
        ->whereNotIn('trang_thai', ['Đang bảo trì', 'Đang lên lịch', 'Từ chối tiếp nhận', 'Chờ thanh toán'])
        ->update(['trang_thai' => 'Đang bảo trì', 'updated_at' => now()]);

    $query = LichBaoTri::with(['taiSan.phong', 'khoTaiSan']);

    if ($request->filled('month') && $request->filled('year')) {
        $query->whereYear('ngay_bao_tri', $request->year)
            ->whereMonth('ngay_bao_tri', $request->month);
    } elseif ($request->filled('year')) {
        $query->whereYear('ngay_bao_tri', $request->year);
    } elseif ($request->filled('month')) {
        $query->whereMonth('ngay_bao_tri', $request->month);
    }

    if ($request->filled('trang_thai')) {
        $query->where('trang_thai', $request->trang_thai);
    }

    if ($request->filled('ngay_bao_tri')) {
        $query->whereDate('ngay_bao_tri', $request->ngay_bao_tri);
    }

    if ($request->filled('vi_tri')) {
        if ($request->vi_tri === 'phong') {
            $query->whereNotNull('tai_san_id');
        } elseif ($request->vi_tri === 'kho') {
            $query->whereNotNull('kho_tai_san_id');
        }
    }

    $thongKe = [
        'dang_len_lich' => LichBaoTri::where('trang_thai', 'Đang lên lịch')->count(),
        'cho_bao_tri' => LichBaoTri::where('trang_thai', 'Chờ bảo trì')->count(),
        'dang_bao_tri' => LichBaoTri::where('trang_thai', 'Đang bảo trì')->count(),
        'hoan_thanh' => LichBaoTri::where('trang_thai', 'Hoàn thành')->count(),
        'tong_tai_san' => LichBaoTri::count(),
    ];

    $lich = $query->orderByRaw("
        CASE 
            WHEN trang_thai = 'Đang lên lịch' THEN 0
            WHEN trang_thai = 'Chờ bảo trì' THEN 1
            WHEN trang_thai = 'Đang bảo trì' THEN 2
            WHEN trang_thai = 'Chờ thanh toán' THEN 3
            WHEN trang_thai = 'Hoàn thành' THEN 4
            ELSE 5
        END ASC
    ")
        ->orderBy('ngay_bao_tri', 'asc')
        ->paginate(6)
        ->appends($request->query());

    $lich->transform(function ($item) {
        $item->trang_thai_client = $item->trang_thai === 'Từ chối tiếp nhận' ? 'Bình thường' : $item->trang_thai;
        return $item;
    });

    return view('lichbaotri.index', compact('lich', 'thongKe'));
}


    /** ➕ Form tạo mới */
    public function create(Request $request)
    {
        $taiSanId = $request->query('taisan_id');

        if ($taiSanId) {
            $taiSan = TaiSan::with(['phong', 'khoTaiSan', 'slots.sinhVien'])->find($taiSanId);
            if (!$taiSan) {
                return redirect()->route('taisan.index')->with('error', 'Không tìm thấy tài sản.');
            }
            return view('lichbaotri.create', [
                'taiSan' => $taiSan,
                'taiSans' => [],
                'phongs' => \App\Models\Phong::all(),
            ]);
        }

        $taiSans = TaiSan::with(['phong', 'khoTaiSan'])->get();
        $phongs = \App\Models\Phong::all();

        return view('lichbaotri.create', [
            'taiSan' => null,
            'taiSans' => $taiSans,
            'phongs' => $phongs,
        ]);
    }

    /** 💾 Lưu lịch bảo trì mới */
    public function store(Request $request)
    {
        $request->validate([
            'tai_san_id' => 'required|array',
            'tai_san_id.*' => 'integer',
            'ngay_bao_tri' => 'required|date',
            'mo_ta' => 'nullable|array',
            'mo_ta.*' => 'nullable|string',
            'chi_phi' => 'nullable|array',
            'chi_phi.*' => 'nullable|numeric|min:0',
            'hinh_anh' => 'nullable|array',
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fileArray = $request->file('hinh_anh', []);
        $today = now()->toDateString();
        $trangThai = $request->ngay_bao_tri > $today ? 'Chờ bảo trì' : 'Đang bảo trì';

        foreach ($request->tai_san_id as $index => $id) {
            $hinhAnhTruoc = null;

            // Lưu hình ảnh nếu có
            if (isset($fileArray[$index]) && $fileArray[$index] instanceof \Illuminate\Http\UploadedFile) {
                $file = $fileArray[$index];
                $fileName = time() . "_{$index}_" . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('uploads/lichbaotri'), $fileName);
                $hinhAnhTruoc = $fileName;
            }

            $taiSan = TaiSan::find($id);
            $khoTaiSan = KhoTaiSan::find($id);

            $taiSanId = $taiSan ? $taiSan->id : null;
            $khoTaiSanId = $khoTaiSan ? $khoTaiSan->id : null;

            // Kiểm tra lịch tồn tại
            $existing = LichBaoTri::where(function ($q) use ($taiSanId, $khoTaiSanId) {
                    if ($taiSanId) $q->where('tai_san_id', $taiSanId);
                    if ($khoTaiSanId) $q->orWhere('kho_tai_san_id', $khoTaiSanId);
                })
                ->whereIn('trang_thai', ['Chờ bảo trì', 'Đang bảo trì'])
                ->first();

            if ($existing) {
                return redirect()->back()->with('error', 'Tài sản này đang có bảo trì chưa hoàn thành.');
            }

            // Tạo lịch bảo trì mới
            LichBaoTri::create([
                'tai_san_id' => $taiSanId,
                'kho_tai_san_id' => $khoTaiSanId,
                'ngay_bao_tri' => $request->ngay_bao_tri,
                'mo_ta' => $request->mo_ta[$index] ?? null,
                'hinh_anh_truoc' => $hinhAnhTruoc,
                'trang_thai' => $trangThai,
                'chi_phi' => $request->chi_phi[$index] ?? 0,
                'nguoi_tao' => 'admin', // Admin tạo lịch bảo trì
            ]);

            // Cập nhật trạng thái tài sản
            if ($trangThai === 'Đang bảo trì') {
                if ($taiSan) $taiSan->update(['tinh_trang_hien_tai' => 'Đang bảo trì']);
                if ($khoTaiSan) $khoTaiSan->update(['tinh_trang' => 'Đang bảo trì']);
            }
        }

        return redirect()->route('lichbaotri.index')
            ->with('success', 'Đã thêm lịch bảo trì cho tài sản thành công!');
    }

    /** ✅ Hoàn thành bảo trì */
public function hoanThanhSubmit(Request $request, $id)
{
    $lich = LichBaoTri::findOrFail($id);

    // Validate dữ liệu
    $request->validate([
        'ngay_hoan_thanh' => 'required|date',
        'mo_ta_sau' => 'nullable|string',
        'chi_phi' => 'nullable|numeric|min:0',
        'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'ktx_thanh_toan' => 'nullable|boolean',
    ]);

    // Cập nhật thông tin bảo trì
    $lich->ngay_hoan_thanh = $request->ngay_hoan_thanh;
    $lich->mo_ta_sau = $request->mo_ta_sau;
    $lich->chi_phi = $request->chi_phi ?? 0;

    // Lưu ảnh nếu có
    if ($request->hasFile('hinh_anh')) {
        $file = $request->file('hinh_anh');
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Xóa ảnh cũ nếu có
        if ($lich->hinh_anh && file_exists(public_path('uploads/lichbaotri/' . $lich->hinh_anh))) {
            unlink(public_path('uploads/lichbaotri/' . $lich->hinh_anh));
        }

        $file->move(public_path('uploads/lichbaotri'), $filename);
        $lich->hinh_anh = $filename;
    }

    // Cập nhật trạng thái tài sản trở về bình thường sau bảo trì
    $lich->loadMissing(['taiSan', 'khoTaiSan']);
    if ($lich->taiSan) $lich->taiSan->update(['tinh_trang_hien_tai' => 'Bình thường']);
    if ($lich->khoTaiSan) $lich->khoTaiSan->update(['tinh_trang' => 'Bình thường']);

    // Kiểm tra checkbox KTX thanh toán
    $ktxThanhToan = $request->has('ktx_thanh_toan') && $request->ktx_thanh_toan;

    if ($ktxThanhToan) {
        // KTX thanh toán → Hoàn thành luôn, không tạo hóa đơn cho sinh viên
        $lich->trang_thai = 'Hoàn thành';
        $lich->save();

        return redirect()->back()->with('success', 'Hoàn thành bảo trì thành công! Chi phí do KTX thanh toán.');
    } else {
        // Sinh viên thanh toán → Chờ thanh toán, tạo hóa đơn
        $lich->trang_thai = 'Chờ thanh toán';
        $lich->save();

        // Tạo hóa đơn bảo trì cho sinh viên
        HoaDonBaoTri::create([
            'lich_bao_tri_id' => $lich->id,
            'chi_phi' => $request->chi_phi ?? 0,
            'trang_thai_thanh_toan' => 'Chưa thanh toán',
            'phuong_thuc_thanh_toan' => null,
            'ghi_chu' => 'Tự động tạo khi hoàn thành bảo trì',
        ]);

        return redirect()->back()->with('success', 'Hoàn thành bảo trì và tạo hóa đơn cho sinh viên thanh toán!');
    }
}


    /** 🗑️ Xóa lịch bảo trì */
    public function destroy($id)
    {
        $lich = LichBaoTri::findOrFail($id);

        foreach (['hinh_anh_truoc', 'hinh_anh'] as $imgField) {
            if ($lich->$imgField && file_exists(public_path('uploads/lichbaotri/' . $lich->$imgField))) {
                unlink(public_path('uploads/lichbaotri/' . $lich->$imgField));
            }
        }

        $lich->delete();
        return redirect()->route('lichbaotri.index')->with('success', 'Xóa lịch bảo trì thành công!');
    }

    /** 👁️ Xem chi tiết (modal) */
    public function show($id)
    {
        $lich = LichBaoTri::with([
            'taiSan.phong',
            'taiSan.slots.sinhVien',
            'khoTaiSan'
        ])->findOrFail($id);
        return view('lichbaotri._modal', compact('lich'));
    }

    /** ✏️ Form chỉnh sửa */
    public function edit($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);
        $taiSan = TaiSan::all();
        $khoTaiSan = KhoTaiSan::all();
        return view('lichbaotri.edit', compact('lichBaoTri', 'taiSan', 'khoTaiSan'));
    }

    /** 💾 Cập nhật thông tin lịch bảo trì */
    public function update(Request $request, $id)
    {
        $lich = LichBaoTri::findOrFail($id);
        $trangThaiCu = $lich->trang_thai;

        $lich->ngay_bao_tri = $request->ngay_bao_tri;
        $lich->ngay_hoan_thanh = $request->ngay_hoan_thanh;
        $lich->mo_ta = $request->mo_ta;
        $lich->trang_thai = $request->trang_thai;
        $lich->chi_phi = $request->chi_phi; // ✅ cập nhật chi phí

        if ($request->hasFile('hinh_anh_truoc')) {
            $file = $request->file('hinh_anh_truoc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
            $lich->hinh_anh_truoc = $fileName;
        }

        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
            $lich->hinh_anh = $fileName;
        }

        $lich->save();

        if ($trangThaiCu !== 'Đang bảo trì' && $request->trang_thai === 'Đang bảo trì') {
            $lich->loadMissing(['taiSan', 'khoTaiSan']);

            if ($lich->taiSan) $lich->taiSan->update(['tinh_trang_hien_tai' => 'Đang bảo trì']);
            if ($lich->khoTaiSan) $lich->khoTaiSan->update(['tinh_trang' => 'Đang bảo trì']);
        }

        return redirect()->route('lichbaotri.index')->with('success', 'Cập nhật lịch bảo trì thành công!');
    }



    /** 🔹 Lấy danh sách loại tài sản trong kho */
    public function getLoaiTaiSan()
    {
        $data = \App\Models\LoaiTaiSan::whereHas('khoTaiSan')
            ->select('id', 'ten_loai')
            ->get();

        return response()->json($data);
    }

    /** 🔹 Lấy tài sản trong KHO theo loại */
    /** 🔹 Lấy tài sản trong KHO */
    public function getTaiSanKho($loaiId)
    {
        $data = KhoTaiSan::where('loai_id', $loaiId)
            ->whereDoesntHave('lichBaoTri', function ($q) {
                $q->whereIn('trang_thai', ['Chờ bảo trì', 'Đang bảo trì']);
            })
            ->get()
            ->map(function ($ts) {
                return [
                    'id' => $ts->id,
                    'ma_tai_san' => $ts->ma_tai_san ?? 'Không có mã',
                    'ten_tai_san' => $ts->ten_tai_san,
                    'hinh_anh' => $ts->hinh_anh
                        ? asset('storage/' . $ts->hinh_anh)
                        : asset('images/no-image.png'),
                ];
            });

        return response()->json($data);
    }

    /** 🔹 Lấy tài sản trong PHÒNG theo phòng_id */
    /** 🔹 Lấy tài sản trong PHÒNG theo phòng_id */
    public function getTaiSanPhong($phongId)
    {
        $taiSans = TaiSan::with(['khoTaiSan', 'slots.sinhVien'])
            ->where('phong_id', $phongId)
            ->whereDoesntHave('lichBaoTri', function ($q) {
                $q->whereIn('trang_thai', ['Chờ bảo trì', 'Đang bảo trì']);
            })
            ->get()
            ->map(function ($ts) {
                $slot = $ts->slots->first();
                $sinhVien = $slot?->sinhVien;

                return [
                    'id' => $ts->id,
                    'ma_tai_san' => $ts->khoTaiSan->ma_tai_san ?? 'Không có mã',
                    'ten_tai_san' => $ts->ten_tai_san,
                    'so_luong' => $ts->so_luong,
                    'hinh_anh' => $ts->khoTaiSan && $ts->khoTaiSan->hinh_anh
                        ? asset('storage/' . $ts->khoTaiSan->hinh_anh)
                        : asset('images/no-image.png'),
                    'nguoi_su_dung' => $sinhVien?->ho_ten ?? 'Tài sản chung',
                    'ma_sinh_vien' => $sinhVien?->ma_sinh_vien ?? null,
                    'ma_slot' => $slot?->ma_slot ?? null,
                ];
            });

        return response()->json($taiSans);
    }



    /** ✅ Tiếp nhận báo hỏng - chuyển từ "Đang lên lịch" sang "Chờ bảo trì" hoặc "Đang bảo trì" */
    public function tiepNhan($id)
    {
        $lich = LichBaoTri::findOrFail($id);

        // Chỉ tiếp nhận khi trạng thái là "Đang lên lịch"
        if ($lich->trang_thai !== 'Đang lên lịch') {
            return redirect()->route('lichbaotri.index')
                ->with('error', 'Chỉ có thể tiếp nhận các yêu cầu đang ở trạng thái "Đang lên lịch".');
        }

        $today = now()->toDateString();

        // Xác định trạng thái mới dựa trên ngày bảo trì
        if ($lich->ngay_bao_tri > $today) {
            $trangThaiMoi = 'Chờ bảo trì';
        } else {
            $trangThaiMoi = 'Đang bảo trì';
        }

        // Cập nhật trạng thái lịch bảo trì
        $lich->trang_thai = $trangThaiMoi;
        $lich->save();

        // Cập nhật trạng thái tài sản (chỉ khi đang bảo trì, không phải chờ bảo trì)
        $lich->loadMissing(['taiSan', 'khoTaiSan']);

        if ($trangThaiMoi === 'Đang bảo trì') {
            if ($lich->taiSan) {
                $lich->taiSan->update([
                    'tinh_trang_hien_tai' => 'Đang bảo trì'
                ]);
            }

            if ($lich->khoTaiSan) {
                $lich->khoTaiSan->update([
                    'tinh_trang' => 'Đang bảo trì'
                ]);
            }
        }

        return redirect()->route('lichbaotri.index')
            ->with('success', "Đã tiếp nhận báo hỏng và chuyển sang trạng thái '{$trangThaiMoi}'.");
    }
    /** ❌ Từ chối tiếp nhận báo hỏng */
    /** ❌ Từ chối tiếp nhận báo hỏng */
    public function tuChoi(Request $request, $id)
    {
        $lich = LichBaoTri::findOrFail($id);

        if ($lich->trang_thai !== 'Đang lên lịch') {
            return redirect()->route('lichbaotri.index')
                ->with('error', 'Chỉ có thể từ chối các yêu cầu đang ở trạng thái "Đang lên lịch".');
        }

        $request->validate(['ly_do' => 'required|string|max:255']);

        $lich->trang_thai = 'Từ chối tiếp nhận';
        $lich->mo_ta_sau = mb_substr("❌ Lý do từ chối: " . $request->ly_do, 0, 255);
        $lich->save();

        $lich->loadMissing(['taiSan', 'khoTaiSan']);

        if ($lich->taiSan) $lich->taiSan->update(['tinh_trang_hien_tai' => 'Bình thường']);
        if ($lich->khoTaiSan) $lich->khoTaiSan->update(['tinh_trang' => 'Bình thường']);

        return redirect()->route('lichbaotri.index')
            ->with('success', 'Đã từ chối tiếp nhận báo hỏng.');
    }
}
