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

        // ✅ Tự động cập nhật trạng thái
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

        $lich = $query->orderByRaw("
                CASE 
                    WHEN trang_thai = 'Chờ bảo trì' THEN 1
                    WHEN trang_thai = 'Đang bảo trì' THEN 2
                    WHEN trang_thai = 'Hoàn thành' THEN 3
                    ELSE 4
                END ASC
            ")
            ->orderBy('ngay_bao_tri', 'asc')
            ->paginate(6);

        return view('lichbaotri.index', compact('lich'));
    }


    /** ➕ Form tạo mới */
   public function create()
{
    $phongs = \App\Models\Phong::orderBy('ten_phong')->get();
    return view('lichbaotri.create', compact('phongs'));
}


   public function store(Request $request)
{
    $request->validate([
        'tai_san_id' => 'required|integer',
        'ngay_bao_tri' => 'required|date',
        'mo_ta' => 'nullable|string',
        'hinh_anh_truoc' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // 🖼️ Lưu ảnh trước bảo trì (nếu có)
    $hinhAnhTruoc = null;
    if ($request->hasFile('hinh_anh_truoc')) {
        $file = $request->file('hinh_anh_truoc');
        $hinhAnhTruoc = time() . '_truoc_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/lichbaotri'), $hinhAnhTruoc);
    }

    // 🧭 Xác định loại (phòng hay kho)
    // Nếu có tài sản này trong bảng TaiSan → là tài sản phòng
    $taiSan = \App\Models\TaiSan::find($request->tai_san_id);
    $khoTaiSan = \App\Models\KhoTaiSan::find($request->tai_san_id);

    $taiSanId = $taiSan ? $taiSan->id : null;
    $khoTaiSanId = $khoTaiSan ? $khoTaiSan->id : null;

    // 🕐 Tự động set trạng thái
    $today = now()->toDateString();
    $trangThai = $request->ngay_bao_tri > $today ? 'Chờ bảo trì' : 'Đang bảo trì';

    // 💾 Lưu lịch bảo trì
    LichBaoTri::create([
        'tai_san_id' => $taiSanId,
        'kho_tai_san_id' => $khoTaiSanId,
        'ngay_bao_tri' => $request->ngay_bao_tri,
        'mo_ta' => $request->mo_ta,
        'hinh_anh_truoc' => $hinhAnhTruoc,
        'trang_thai' => $trangThai,
    ]);

    return redirect()->route('lichbaotri.index')->with('success', 'Thêm lịch bảo trì thành công!');
}

    public function hoanThanhSubmit(Request $request, $id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        $request->validate([
            'ngay_hoan_thanh' => 'required|date',
            'hinh_anh_sau' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 🖼️ Ảnh sau bảo trì
        $hinhAnhSau = $lichBaoTri->hinh_anh;
        if ($request->hasFile('hinh_anh_sau')) {
            if ($hinhAnhSau && file_exists(public_path('uploads/lichbaotri/' . $hinhAnhSau))) {
                unlink(public_path('uploads/lichbaotri/' . $hinhAnhSau));
            }

            $file = $request->file('hinh_anh_sau');
            $hinhAnhSau = time() . '_sau_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $hinhAnhSau);
        }

        $lichBaoTri->update([
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'hinh_anh' => $hinhAnhSau, // ✅ cột đúng
            'trang_thai' => 'Hoàn thành',
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Cập nhật hoàn thành thành công!');
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

    /** 👁️ Xem chi tiết (hiển thị modal) */
    public function show($id)
    {
        $lich = LichBaoTri::with(['taiSan.phong', 'khoTaiSan'])->findOrFail($id);
        return view('lichbaotri._modal', compact('lich'));
    }
    public function edit($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);
        $taiSan = TaiSan::all();
        $khoTaiSan = KhoTaiSan::all(); // nếu cần dùng trong form
        return view('lichbaotri.edit', compact('lichBaoTri', 'taiSan', 'khoTaiSan'));
    }


    public function update(Request $request, $id)
    {
        // Lấy lịch bảo trì theo ID
        $lich = LichBaoTri::findOrFail($id);

        // Cập nhật các thông tin cơ bản
        $lich->ngay_bao_tri = $request->ngay_bao_tri;
        $lich->ngay_hoan_thanh = $request->ngay_hoan_thanh; // ngày hoàn thành
        $lich->mo_ta = $request->mo_ta;
        $lich->trang_thai = $request->trang_thai; // trạng thái

        // Cập nhật ảnh trước bảo trì nếu có
        if ($request->hasFile('hinh_anh_truoc')) {
            $file = $request->file('hinh_anh_truoc');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
            $lich->hinh_anh_truoc = $fileName;
        }

        // Cập nhật ảnh sau bảo trì nếu có
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
            $lich->hinh_anh = $fileName;
        }

        // Lưu tất cả thay đổi
        $lich->save();

        // Chuyển hướng về danh sách với thông báo thành công
        return redirect()->route('lichbaotri.index')->with('success', 'Cập nhật lịch bảo trì thành công!');
    }
    /** 🔹 Lấy danh sách tài sản theo loại (AJAX cho dropdown phụ thuộc) */
    /** 🔹 Lấy danh sách loại tài sản theo vị trí */
    // 🔹 Lấy danh sách loại tài sản trong kho
public function getLoaiTaiSan()
{
    $data = \App\Models\LoaiTaiSan::whereHas('khoTaiSan')
        ->select('id', 'ten_loai')
        ->get();

    return response()->json($data);
}

// 🔹 Lấy tài sản trong KHO theo loại
public function getTaiSanKho($loaiId)
{
    $data = KhoTaiSan::where('loai_id', $loaiId)
        ->whereDoesntHave('lichBaoTri', function ($q) {
            $q->whereNull('ngay_hoan_thanh');
        })
        ->select('id', 'ten_tai_san', 'so_luong')
        ->get();

    return response()->json($data);
}

// 🔹 Lấy tài sản trong PHÒNG theo phòng_id
public function getTaiSanPhong($phongId)
{
    $taiSans = TaiSan::join('kho_tai_san', 'tai_san.kho_tai_san_id', '=', 'kho_tai_san.id')
        ->where('tai_san.phong_id', $phongId)
        ->select(
            'tai_san.id',
            'kho_tai_san.ma_tai_san', 
            'tai_san.ten_tai_san',
            'tai_san.so_luong'
        )
        ->get();

    return response()->json($taiSans);
}


}
