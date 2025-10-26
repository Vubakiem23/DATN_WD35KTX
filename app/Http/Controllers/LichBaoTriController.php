<?php

namespace App\Http\Controllers;

use App\Models\LichBaoTri;
use App\Models\TaiSan;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LichBaoTriController extends Controller
{
    /** 🧭 Hiển thị danh sách lịch bảo trì */
public function index(Request $request)
{
    $today = \Carbon\Carbon::today()->toDateString();

    // 1️⃣ Hoàn thành (đã có ngày hoàn thành)
    DB::table('lich_bao_tri')
        ->whereNotNull('ngay_hoan_thanh')
        ->where('trang_thai', '!=', 'Hoàn thành')
        ->update(['trang_thai' => 'Hoàn thành', 'updated_at' => now()]);

    // 2️⃣ Chờ bảo trì (ngày bảo trì > hôm nay, chưa hoàn thành)
    DB::table('lich_bao_tri')
        ->whereNull('ngay_hoan_thanh')
        ->whereDate('ngay_bao_tri', '>', $today)
        ->where('trang_thai', '!=', 'Chờ bảo trì')
        ->update(['trang_thai' => 'Chờ bảo trì', 'updated_at' => now()]);

    // 3️⃣ Đang bảo trì (ngày bảo trì <= hôm nay, chưa hoàn thành)
    DB::table('lich_bao_tri')
        ->whereNull('ngay_hoan_thanh')
        ->whereDate('ngay_bao_tri', '<=', $today)
        ->where('trang_thai', '!=', 'Đang bảo trì')
        ->update(['trang_thai' => 'Đang bảo trì', 'updated_at' => now()]);

    // Sau đó load danh sách
    $lich = LichBaoTri::with('taiSan')
        ->orderByRaw("
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
  public function create(Request $request)
{
    $phongs = Phong::all();
    $taiSan = TaiSan::with('phong')->get();

    // 🆕 Lấy id tài sản nếu có trong URL
    $selectedTaiSanId = $request->taisan_id;

    // 🆕 Lấy chi tiết tài sản được chọn (nếu có)
    $selectedTaiSan = null;
    if ($selectedTaiSanId) {
        $selectedTaiSan = TaiSan::with('phong')->find($selectedTaiSanId);
    }

    return view('lichbaotri.create', compact('phongs', 'taiSan', 'selectedTaiSanId', 'selectedTaiSan'));
}


    /** 💾 Lưu lịch bảo trì mới */
    public function store(Request $request)
    {
        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'ngay_bao_tri' => 'required|date',
            'ngay_hoan_thanh' => 'nullable|date',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 📸 Upload ảnh
        $fileName = null;
        if ($request->hasFile('hinh_anh')) {
            if (!file_exists(public_path('uploads/lichbaotri'))) {
                mkdir(public_path('uploads/lichbaotri'), 0777, true);
            }

            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
        }

        // 🔁 Xác định trạng thái ban đầu
        if ($request->ngay_hoan_thanh) {
            $trangThai = 'Hoàn thành';
        } elseif ($request->ngay_bao_tri > now()->toDateString()) {
            $trangThai = 'Chờ bảo trì';
        } else {
            $trangThai = 'Đang bảo trì';
        }

        // 💾 Lưu vào DB
        LichBaoTri::create([
            'tai_san_id' => $request->tai_san_id,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $fileName,
            'trang_thai' => $trangThai,
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Thêm lịch bảo trì thành công!');
    }

    /** ✏️ Form sửa */
    public function edit($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);
        $taiSan = TaiSan::with('phong')->get();
        return view('lichbaotri.edit', compact('lichBaoTri', 'taiSan'));
    }

    /** 🔄 Cập nhật lịch bảo trì */
    public function update(Request $request, $id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'ngay_bao_tri' => 'required|date',
            'ngay_hoan_thanh' => 'nullable|date',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 📸 Upload ảnh mới (nếu có)
        $fileName = $lichBaoTri->hinh_anh;
        if ($request->hasFile('hinh_anh')) {
            if (!file_exists(public_path('uploads/lichbaotri'))) {
                mkdir(public_path('uploads/lichbaotri'), 0777, true);
            }

            if ($fileName && file_exists(public_path('uploads/lichbaotri/' . $fileName))) {
                unlink(public_path('uploads/lichbaotri/' . $fileName));
            }

            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
        }

        // 🔁 Cập nhật trạng thái
        if ($request->ngay_hoan_thanh) {
            $trangThai = 'Hoàn thành';
        } elseif ($request->ngay_bao_tri > now()->toDateString()) {
            $trangThai = 'Chờ bảo trì';
        } else {
            $trangThai = 'Đang bảo trì';
        }

        $lichBaoTri->update([
            'tai_san_id' => $request->tai_san_id,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $fileName,
            'trang_thai' => $trangThai,
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Cập nhật lịch bảo trì thành công!');
    }

    /** ❌ Xóa lịch bảo trì */
    public function destroy($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        if ($lichBaoTri->hinh_anh && file_exists(public_path('uploads/lichbaotri/' . $lichBaoTri->hinh_anh))) {
            unlink(public_path('uploads/lichbaotri/' . $lichBaoTri->hinh_anh));
        }

        $lichBaoTri->delete();

        return redirect()->route('lichbaotri.index')->with('success', 'Đã xóa lịch bảo trì thành công!');
    }

    /** ✅ Đánh dấu hoàn thành */
    public function hoanThanh($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        $lichBaoTri->update([
            'trang_thai' => 'Hoàn thành',
            'ngay_hoan_thanh' => now()->toDateString(),
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Đã cập nhật trạng thái hoàn thành!');
    }
    public function showModal($id)
{
    $lich = LichBaoTri::with('taiSan')->find($id);

    if (!$lich) {
        return response()->json(['data' => '<p class="text-danger">Không tìm thấy lịch bảo trì.</p>']);
    }

    $html = view('lichbaotri._modal', compact('lich'))->render();

    return response()->json(['data' => $html]);
}

}
