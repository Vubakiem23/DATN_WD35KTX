<?php

namespace App\Http\Controllers;

use App\Models\KhoTaiSan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KhoTaiSanController extends Controller
{
    /** 🧱 Danh sách kho */
    public function index(Request $request)
    {
        $query = KhoTaiSan::query();

        if ($search = $request->input('search')) {
            $query->where('ten_tai_san', 'like', "%$search%")
                  ->orWhere('ma_tai_san', 'like', "%$search%");
        }

        $kho = $query->orderBy('id', 'desc')->paginate(6);

        return view('kho.index', compact('kho'));
    }

    /** ➕ Form thêm mới */
    public function create()
    {
        return view('kho.create');
    }

    /** 💾 Lưu dữ liệu */
    public function store(Request $request)
    {
        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'don_vi_tinh' => 'nullable|string|max:50',
            'so_luong' => 'required|integer|min:0',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ghi_chu' => 'nullable|string',
        ]);

        // 📦 Tạo mã tài sản random: ví dụ TS20251021-AB12CD
        $maTaiSan = 'TS' .'-'. strtoupper(Str::random(6));

        $fileName = null;
        if ($request->hasFile('hinh_anh')) {
            if (!file_exists(public_path('uploads/kho'))) {
                mkdir(public_path('uploads/kho'), 0777, true);
            }

            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kho'), $fileName);
        }

        KhoTaiSan::create([
            'ma_tai_san' => $maTaiSan,
            'ten_tai_san' => $request->ten_tai_san,
            'don_vi_tinh' => $request->don_vi_tinh,
            'so_luong' => $request->so_luong,
            'hinh_anh' => $fileName,
            'ghi_chu' => $request->ghi_chu,
        ]);

        return redirect()->route('kho.index')->with('success', 'Thêm tài sản vào kho thành công!');
    }

    /** ✏️ Sửa */
    public function edit($id)
    {
        $kho = KhoTaiSan::findOrFail($id);
        return view('kho.edit', compact('kho'));
    }

    /** 🔄 Cập nhật */
    public function update(Request $request, $id)
    {
        $kho = KhoTaiSan::findOrFail($id);

        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'don_vi_tinh' => 'nullable|string|max:50',
            'so_luong' => 'required|integer|min:0',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ghi_chu' => 'nullable|string',
        ]);

        $fileName = $kho->hinh_anh;
        if ($request->hasFile('hinh_anh')) {
            if (!file_exists(public_path('uploads/kho'))) {
                mkdir(public_path('uploads/kho'), 0777, true);
            }

            if ($fileName && file_exists(public_path('uploads/kho/' . $fileName))) {
                unlink(public_path('uploads/kho/' . $fileName));
            }

            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kho'), $fileName);
        }

        $kho->update([
            'ten_tai_san' => $request->ten_tai_san,
            'don_vi_tinh' => $request->don_vi_tinh,
            'so_luong' => $request->so_luong,
            'hinh_anh' => $fileName,
            'ghi_chu' => $request->ghi_chu,
        ]);

        return redirect()->route('kho.index')->with('success', 'Cập nhật tài sản kho thành công!');
    }

    /** ❌ Xóa */
    public function destroy($id)
    {
        $kho = KhoTaiSan::findOrFail($id);

        if ($kho->hinh_anh && file_exists(public_path('uploads/kho/' . $kho->hinh_anh))) {
            unlink(public_path('uploads/kho/' . $kho->hinh_anh));
        }

        $kho->delete();

        return redirect()->route('kho.index')->with('success', 'Đã xóa tài sản khỏi kho!');
    }
    public function showModal($id)
{
    $taiSan = KhoTaiSan::find($id); // hoặc model bạn đang dùng (ví dụ: TaiSanKho)
    if (!$taiSan) {
        return response()->json(['data' => '<p class="text-danger">Không tìm thấy tài sản.</p>']);
    }

    $html = view('kho._modal', compact('taiSan'))->render();
    return response()->json(['data' => $html]);
}

}
