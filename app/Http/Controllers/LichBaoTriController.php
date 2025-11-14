<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LichBaoTri;
use App\Models\TaiSan;
use App\Models\KhoTaiSan;
use Illuminate\Support\Facades\DB;

class LichBaoTriController extends Controller
{
    /** 🧭 Hiển thị danh sách lịch bảo trì */
    public function index(Request $request)
    {
        $today = now()->toDateString();

        // ✅ Tự động cập nhật trạng thái lịch bảo trì
        DB::table('lich_bao_tri')
            ->whereNotNull('ngay_hoan_thanh')
            ->where('trang_thai', '!=', 'Hoàn thành')
            ->update(['trang_thai' => 'Hoàn thành', 'updated_at' => now()]);

        DB::table('lich_bao_tri')
            ->whereNull('ngay_hoan_thanh')
            ->whereDate('ngay_bao_tri', '>', $today)
            ->where('trang_thai', '!=', 'Chờ bảo trì')
            ->update(['trang_thai' => 'Chờ bảo trì', 'updated_at' => now()]);

        DB::table('lich_bao_tri')
            ->whereNull('ngay_hoan_thanh')
            ->whereDate('ngay_bao_tri', '<=', $today)
            ->where('trang_thai', '!=', 'Đang bảo trì')
            ->update(['trang_thai' => 'Đang bảo trì', 'updated_at' => now()]);

        // 🧩 Bộ lọc
        $query = LichBaoTri::with(['taiSan.phong', 'khoTaiSan']);

        // Lọc theo tháng/năm
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

        // 📊 Thống kê số tài sản cần bảo trì
        $thongKe = [
            'cho_bao_tri' => LichBaoTri::where('trang_thai', 'Chờ bảo trì')->count(),
            'dang_bao_tri' => LichBaoTri::where('trang_thai', 'Đang bảo trì')->count(),
            'hoan_thanh' => LichBaoTri::where('trang_thai', 'Hoàn thành')->count(),
            'tong_tai_san' => LichBaoTri::count(),
        ];

        // Áp dụng bộ lọc tháng/năm cho thống kê nếu có
        if ($request->filled('month') && $request->filled('year')) {
            $thongKeQuery = LichBaoTri::whereYear('ngay_bao_tri', $request->year)
                ->whereMonth('ngay_bao_tri', $request->month);
            $thongKe['cho_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Chờ bảo trì')->count();
            $thongKe['dang_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Đang bảo trì')->count();
            $thongKe['hoan_thanh'] = (clone $thongKeQuery)->where('trang_thai', 'Hoàn thành')->count();
            $thongKe['tong_tai_san'] = $thongKeQuery->count();
        } elseif ($request->filled('year')) {
            $thongKeQuery = LichBaoTri::whereYear('ngay_bao_tri', $request->year);
            $thongKe['cho_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Chờ bảo trì')->count();
            $thongKe['dang_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Đang bảo trì')->count();
            $thongKe['hoan_thanh'] = (clone $thongKeQuery)->where('trang_thai', 'Hoàn thành')->count();
            $thongKe['tong_tai_san'] = $thongKeQuery->count();
        } elseif ($request->filled('month')) {
            $thongKeQuery = LichBaoTri::whereMonth('ngay_bao_tri', $request->month);
            $thongKe['cho_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Chờ bảo trì')->count();
            $thongKe['dang_bao_tri'] = (clone $thongKeQuery)->where('trang_thai', 'Đang bảo trì')->count();
            $thongKe['hoan_thanh'] = (clone $thongKeQuery)->where('trang_thai', 'Hoàn thành')->count();
            $thongKe['tong_tai_san'] = $thongKeQuery->count();
        }

        $lich = $query->orderByRaw("
                CASE 
                    WHEN trang_thai = 'Chờ bảo trì' THEN 1
                    WHEN trang_thai = 'Đang bảo trì' THEN 2
                    WHEN trang_thai = 'Hoàn thành' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('ngay_bao_tri', 'asc')
            ->paginate(6)
            ->appends($request->query());

        return view('lichbaotri.index', compact('lich', 'thongKe'));
    }

    /** ➕ Form tạo mới */
    public function create(Request $request)
    {
        $taiSanId = $request->query('taisan_id');

        // Nếu đi từ nút “Bảo trì” trong danh sách tài sản
        if ($taiSanId) {
            $taiSan = TaiSan::with(['phong', 'khoTaiSan', 'slots.sinhVien'])->find($taiSanId);

            if (!$taiSan) {
                return redirect()
                    ->route('taisan.index')
                    ->with('error', 'Không tìm thấy tài sản.');
            }

            return view('lichbaotri.create', [
                'taiSan' => $taiSan, // 1 tài sản duy nhất
                'taiSans' => [], // để view không hiển thị danh sách nhiều tài sản
                'phongs' => \App\Models\Phong::all(), // cần cho form nếu có dùng tới
            ]);
        }

        // Nếu vào form tạo lịch bảo trì thủ công
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
            'hinh_anh' => 'nullable|array',
            'hinh_anh.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Lấy mảng file ảnh (nếu có)
        $fileArray = $request->file('hinh_anh', []); // Trả về array hoặc []

        $today = now()->toDateString();
        $trangThai = $request->ngay_bao_tri > $today ? 'Chờ bảo trì' : 'Đang bảo trì';

        foreach ($request->tai_san_id as $index => $id) {
            $hinhAnhTruoc = null;

            // 🖼️ Nếu có file ứng với chỉ số này → lưu
            if (isset($fileArray[$index]) && $fileArray[$index] instanceof \Illuminate\Http\UploadedFile) {
                $file = $fileArray[$index];

                // Đặt tên file an toàn, tránh trùng
                $fileName = time() . "_{$index}_" . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                // Lưu vào thư mục public/uploads/lichbaotri
                $file->move(public_path('uploads/lichbaotri'), $fileName);

                $hinhAnhTruoc = $fileName;
            }

            // 🔍 Kiểm tra xem ID thuộc tài sản trong phòng hay trong kho
            $taiSan = TaiSan::find($id);
            $khoTaiSan = KhoTaiSan::find($id);

            $taiSanId = $taiSan ? $taiSan->id : null;
            $khoTaiSanId = $khoTaiSan ? $khoTaiSan->id : null;

            // 💾 Tạo bản ghi lịch bảo trì
            LichBaoTri::create([
                'tai_san_id' => $taiSanId,
                'kho_tai_san_id' => $khoTaiSanId,
                'ngay_bao_tri' => $request->ngay_bao_tri,
                'mo_ta' => $request->mo_ta[$index] ?? null,
                'hinh_anh_truoc' => $hinhAnhTruoc,
                'trang_thai' => $trangThai,
            ]);

            // 🔧 Cập nhật trạng thái tài sản
            if ($taiSan) {
                $taiSan->update(['tinh_trang_hien_tai' => 'Đang bảo trì']);
            } elseif ($khoTaiSan) {
                $khoTaiSan->update(['tinh_trang' => 'Đang bảo trì']);
            }
        }

        return redirect()->route('lichbaotri.index')
            ->with('success', 'Đã thêm lịch bảo trì cho nhiều tài sản thành công!');
    }

    /** ✅ Hoàn thành bảo trì */
    /** ✅ Hoàn thành bảo trì */
public function hoanthanhSubmit(Request $request, $id)
{
    $lich = LichBaoTri::findOrFail($id);

    $request->validate([
        'ngay_hoan_thanh' => 'required|date',
        'mo_ta_sau' => 'nullable|string',
        'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // 🖼️ Lưu ảnh sau bảo trì (nếu có)
    if ($request->hasFile('hinh_anh')) {
        $file = $request->file('hinh_anh');
        $fileName = time() . '_sau_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('uploads/lichbaotri'), $fileName);
        $lich->hinh_anh = $fileName; // ✅ Đổi thành đúng tên cột
    }

    // 🗓️ Cập nhật thông tin hoàn thành
    $lich->ngay_hoan_thanh = $request->ngay_hoan_thanh;
    $lich->mo_ta_sau = $request->mo_ta_sau;
    $lich->trang_thai = 'Hoàn thành';
    $lich->save();

    $lich->loadMissing(['taiSan', 'khoTaiSan']);

    if ($lich->taiSan) {
        $lich->taiSan->update([
            'tinh_trang_hien_tai' => 'Bình thường',
        ]);
    }

    if ($lich->khoTaiSan) {
        $lich->khoTaiSan->update([
            'tinh_trang' => 'Bình thường',
        ]);
    }

    return redirect()->route('lichbaotri.index')->with('success', 'Đã cập nhật hoàn thành bảo trì.');
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

        $lich->ngay_bao_tri = $request->ngay_bao_tri;
        $lich->ngay_hoan_thanh = $request->ngay_hoan_thanh;
        $lich->mo_ta = $request->mo_ta;
        $lich->trang_thai = $request->trang_thai;

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

                    // ✅ Add thêm dữ liệu gửi ra UI
                    'nguoi_su_dung' => $sinhVien?->ho_ten ?? 'Tài sản chung',
                    'ma_sinh_vien' => $sinhVien?->ma_sinh_vien ?? null,
                    'ma_slot' => $slot?->ma_slot ?? null,
                ];
            });

        return response()->json($taiSans);
    }
}


