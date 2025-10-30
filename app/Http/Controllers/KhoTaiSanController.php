<?php

namespace App\Http\Controllers;

use App\Models\LoaiTaiSan;
use App\Models\KhoTaiSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KhoTaiSanController extends Controller

{
    /** 🧱 Trang kho: hiển thị danh sách loại tài sản */
    public function index()
    {
        $loaiTaiSan = LoaiTaiSan::withSum('khoTaiSan', 'so_luong')
            ->orderBy('id', 'desc')
            ->paginate(8);
        return view('kho.index', compact('loaiTaiSan'));
    }

    /** 🔁 Hiển thị các tài sản cùng loại */
    public function related(Request $request, $loai_id)
    {
        $loai = LoaiTaiSan::findOrFail($loai_id);

        $query = KhoTaiSan::where('loai_id', $loai_id);

        // Lọc theo tình trạng nếu có
        if ($request->filled('tinh_trang')) {
            $query->where('tinh_trang', $request->tinh_trang);
        }

        // Lọc theo mã tài sản nếu có
        if ($request->filled('ma_tai_san')) {
            $query->where('ma_tai_san', 'like', '%' . $request->ma_tai_san . '%');
        }

        $taiSan = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();

        return view('kho.related', compact('loai', 'taiSan'));
    }


    /** ➕ Hiển thị form thêm tài sản mới cho loại này */
    public function create($loai_id)
    {
        $loai = LoaiTaiSan::findOrFail($loai_id);
        $tinhTrangOptions = ['Mới', 'Hỏng', 'Cũ', 'Bảo trì', 'Bình thường'];
        return view('kho.create', compact('loai', 'tinhTrangOptions'));
    }


    public function store(Request $request, $loai_id)
    {
        $loai = LoaiTaiSan::findOrFail($loai_id);

        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'don_vi_tinh' => 'nullable|string|max:50',
            'tinh_trang' => 'nullable|in:Mới,Hỏng,Cũ,Bảo trì,Bình thường',
            'ghi_chu' => 'nullable|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('kho', 'public');
        }

        $quantity = $request->quantity ?? 1;

        for ($i = 0; $i < $quantity; $i++) {
            KhoTaiSan::create([
                'ma_tai_san' => $this->generateMaTaiSan(),
                'loai_id' => $loai->id,
                'ten_tai_san' => $loai->ten_loai, // Tên mặc định
                'so_luong' => 1,
                'don_vi_tinh' => $request->don_vi_tinh,
                'tinh_trang' => $request->tinh_trang,
                'ghi_chu' => $request->ghi_chu,
                'hinh_anh' => $hinhAnhPath,
            ]);
        }

        return redirect()->route('kho.related', $loai_id)
            ->with('success', "Đã tạo $quantity tài sản mới cho loại {$loai->ten_loai}!");
    }

    public function edit($id)
    {
        $taiSan = KhoTaiSan::findOrFail($id);
        $tinhTrangOptions = ['Mới', 'Hỏng', 'Cũ', 'Bảo trì', 'Bình thường'];
        return view('kho.edit', compact('taiSan', 'tinhTrangOptions'));
    }

  public function update(Request $request, $id)
{
    $taiSan = KhoTaiSan::findOrFail($id);

    $request->validate([
        'ten_tai_san' => 'required|string|max:255',
        'so_luong' => 'required|integer|min:1',
        'don_vi_tinh' => 'nullable|string|max:50',
        'tinh_trang' => 'nullable|in:Mới,Hỏng,Cũ,Bảo trì,Bình thường',
        'ghi_chu' => 'nullable|string',
        'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $hinhAnhPath = $taiSan->hinh_anh; // giữ lại ảnh cũ nếu không upload ảnh mới

    if ($request->hasFile('hinh_anh')) {
        // xóa ảnh cũ
        if ($hinhAnhPath && Storage::disk('public')->exists($hinhAnhPath)) {
            Storage::disk('public')->delete($hinhAnhPath);
        }
        // lưu ảnh mới
        $hinhAnhPath = $request->file('hinh_anh')->store('kho', 'public');
    }

    $taiSan->update([
        'ten_tai_san' => $request->ten_tai_san,
        'so_luong' => $request->so_luong,
        'don_vi_tinh' => $request->don_vi_tinh,
        'tinh_trang' => $request->tinh_trang,
        'ghi_chu' => $request->ghi_chu,
        'hinh_anh' => $hinhAnhPath, // 👈 giữ ảnh cũ hoặc cập nhật ảnh mới
    ]);

    return redirect()->route('kho.related', $taiSan->loai_id)
        ->with('success', 'Cập nhật tài sản thành công!');
}

    /** 🗑️ Xóa tài sản khỏi kho */
    public function destroy($id)
    {
        $taiSan = KhoTaiSan::findOrFail($id);

        // Xóa hình ảnh nếu có
        if ($taiSan->hinh_anh && Storage::disk('public')->exists($taiSan->hinh_anh)) {
            Storage::disk('public')->delete($taiSan->hinh_anh);
        }

        $loai_id = $taiSan->loai_id;
        $taiSan->delete();

        return redirect()->route('kho.related', $loai_id)
            ->with('success', 'Đã xóa tài sản khỏi kho!');
    }

    /** 🔧 Hàm sinh mã tài sản tự động */
    private function generateMaTaiSan()
    {
        do {
            $code = 'TS' . rand(1000, 9999);
        } while (KhoTaiSan::where('ma_tai_san', $code)->exists());

        return $code;
    }
}
