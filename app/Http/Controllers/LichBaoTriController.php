<?php

namespace App\Http\Controllers;

use App\Models\KhoTaiSan;
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

        // 1️⃣ Hoàn thành
        DB::table('lich_bao_tri')
            ->whereNotNull('ngay_hoan_thanh')
            ->where('trang_thai', '!=', 'Hoàn thành')
            ->update(['trang_thai' => 'Hoàn thành', 'updated_at' => now()]);

        // 2️⃣ Chờ bảo trì
        DB::table('lich_bao_tri')
            ->whereNull('ngay_hoan_thanh')
            ->whereDate('ngay_bao_tri', '>', $today)
            ->where('trang_thai', '!=', 'Chờ bảo trì')
            ->update(['trang_thai' => 'Chờ bảo trì', 'updated_at' => now()]);

        // 3️⃣ Đang bảo trì
        DB::table('lich_bao_tri')
            ->whereNull('ngay_hoan_thanh')
            ->whereDate('ngay_bao_tri', '<=', $today)
            ->where('trang_thai', '!=', 'Đang bảo trì')
            ->update(['trang_thai' => 'Đang bảo trì', 'updated_at' => now()]);

        // Load danh sách kèm info phòng/kho
        $lich = LichBaoTri::with(['taiSan', 'taiSan.phong', 'taiSan.khoTaiSan'])
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
  public function create()
    {
        // Tài sản trong phòng
        $taiSanPhong = TaiSan::with('phong')->whereNotNull('phong_id')->get();

        // Tài sản trong kho
        $khoTaiSans = KhoTaiSan::all();

        return view('lichbaotri.create', compact('taiSanPhong', 'khoTaiSans'));
    }

    /** 💾 Lưu lịch bảo trì mới */
    public function store(Request $request)
    {
        $request->validate([
            'tai_san_or_kho' => 'required|string',
            'ngay_bao_tri' => 'required|date',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload ảnh minh chứng
        $fileName = null;
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/lichbaotri'), $fileName);
        }

        // Phân biệt phòng hay kho
        $taiSanId = null;
        $khoTaiSanId = null;

        if (str_starts_with($request->tai_san_or_kho, 'ts_')) {
            $taiSanId = (int) str_replace('ts_', '', $request->tai_san_or_kho);
        } else {
            $khoTaiSanId = (int) str_replace('kho_', '', $request->tai_san_or_kho);
        }

        // Xác định trạng thái
        $today = now()->toDateString();
        $trangThai = $request->ngay_bao_tri > $today ? 'Chờ bảo trì' : 'Đang bảo trì';

        // Tạo lịch bảo trì
        LichBaoTri::create([
            'tai_san_id' => $taiSanId,
            'kho_tai_san_id' => $khoTaiSanId,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $fileName,
            'trang_thai' => $trangThai,
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Thêm lịch bảo trì thành công!');
    }



    // Route Ajax lọc tài sản theo phòng/kho
    public function getTaiSanByPhong(Request $request)
    {
        $phongId = $request->phong_id;

        $taiSan = TaiSan::with(['phong', 'khoTaiSan'])
            ->when($phongId != 'kho', function ($q) use ($phongId) {
                $q->where('phong_id', $phongId);
            })
            ->when($phongId == 'kho', function ($q) {
                $q->whereNotNull('kho_tai_san_id');
            })
            ->get();

        return response()->json($taiSan);
    }


    /** 💾 Lưu lịch bảo trì mới */

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

        // Upload ảnh mới
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

        // Cập nhật trạng thái
        if ($request->ngay_hoan_thanh) {
            $trangThai = 'Hoàn thành';
        } elseif ($request->ngay_bao_tri > now()->toDateString()) {
            $trangThai = 'Chờ bảo trì';
        } else {
            $trangThai = 'Đang bảo trì';
        }

        // Lấy thông tin tài sản
        $taiSan = TaiSan::find($request->tai_san_id);

        $lichBaoTri->update([
            'tai_san_id' => $request->tai_san_id,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'mo_ta' => $request->mo_ta,
            'hinh_anh' => $fileName,
            'trang_thai' => $trangThai,
            'location_type' => $taiSan->phong_id ? 'phong' : 'kho',
            'location_id' => $taiSan->phong_id ?? null,
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

    /** 🔍 Modal chi tiết */
    public function showModal($id)
    {
        $lich = LichBaoTri::with('taiSan', 'taiSan.phong')->find($id);

        if (!$lich) {
            return response()->json(['data' => '<p class="text-danger">Không tìm thấy lịch bảo trì.</p>']);
        }

        $html = view('lichbaotri._modal', compact('lich'))->render();

        return response()->json(['data' => $html]);
    }
}
