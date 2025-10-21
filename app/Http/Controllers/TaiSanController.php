<?php

namespace App\Http\Controllers;

use App\Models\TaiSan;
use App\Models\KhoTaiSan;
use App\Models\Phong;
use Illuminate\Http\Request;

class TaiSanController extends Controller
{
    /** 📋 Danh sách tài sản trong phòng */
public function index(Request $request)
{
    $search = $request->input('search');
    $phongId = $request->input('phong_id');

    $listTaiSan = TaiSan::with(['phong', 'khoTaiSan'])
        ->when($search, function ($query, $search) {
            $query->whereHas('khoTaiSan', function ($q) use ($search) {
                $q->where('ten_tai_san', 'like', "%$search%")
                  ->orWhere('ma_tai_san', 'like', "%$search%");
            });
        })
        ->when($phongId, function ($query, $phongId) {
            $query->where('phong_id', $phongId);
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

    $phongs = \App\Models\Phong::orderBy('ten_phong')->get(); // 👈 thêm dòng này

    return view('taisan.index', compact('listTaiSan', 'phongs'));
}

    /** ➕ Hiển thị form thêm tài sản */
    public function create()
    {
        $phongs = Phong::all();
        $khoTaiSans = KhoTaiSan::where('so_luong', '>', 0)->orderBy('ten_tai_san')->get(); // 📦 chỉ lấy còn hàng
        return view('taisan.create', compact('phongs', 'khoTaiSans'));
    }

    /** 💾 Lưu tài sản mới + trừ số lượng kho */
    public function store(Request $request)
    {
        $request->validate([
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
            'phong_id' => 'nullable|exists:phong,id',
        ]);

        $kho = KhoTaiSan::findOrFail($request->kho_tai_san_id);

        // 🔸 Kiểm tra số lượng còn đủ không
        if ($kho->so_luong < $request->so_luong) {
            return back()->with('error', 'Số lượng trong kho không đủ để cấp cho phòng!');
        }

        // 🔸 Tạo tài sản phòng
        TaiSan::create([
            'kho_tai_san_id' => $request->kho_tai_san_id,
            'ten_tai_san' => $kho->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
            'phong_id' => $request->phong_id,
            'hinh_anh' => $kho->hinh_anh,
        ]);

        // 🔸 Trừ số lượng trong kho
        $kho->so_luong -= $request->so_luong;
        $kho->save();

        return redirect()->route('taisan.index')->with('success', 'Thêm tài sản cho phòng thành công và đã trừ kho!');
    }

    /** ✏️ Form chỉnh sửa */
    public function edit($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $phongs = Phong::all();
        $khoTaiSans = KhoTaiSan::orderBy('ten_tai_san')->get();
        return view('taisan.edit', compact('taiSan', 'phongs', 'khoTaiSans'));
    }

    /** 🔄 Cập nhật tài sản */
    public function update(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);

        $request->validate([
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
            'phong_id' => 'nullable|exists:phong,id',
        ]);

        $kho = KhoTaiSan::findOrFail($request->kho_tai_san_id);

        $taiSan->update([
            'kho_tai_san_id' => $request->kho_tai_san_id,
            'ten_tai_san' => $kho->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
            'phong_id' => $request->phong_id,
            'hinh_anh' => $kho->hinh_anh,
        ]);

        return redirect()->route('taisan.index')->with('success', 'Cập nhật thông tin tài sản thành công!');
    }

    /** ❌ Xóa tài sản + cộng lại số lượng về kho */
    public function destroy($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $kho = KhoTaiSan::find($taiSan->kho_tai_san_id);

        if ($kho) {
            $kho->so_luong += $taiSan->so_luong;
            $kho->save();
        }

        $taiSan->delete();

        return redirect()->route('taisan.index')->with('success', 'Đã xóa tài sản khỏi phòng và hoàn kho thành công!');
    }
}
