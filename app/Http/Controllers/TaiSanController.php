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
        $phongs = Phong::orderBy('ten_phong')->get();

        $listTaiSan = TaiSan::with(['khoTaiSan', 'phong'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('khoTaiSan', function ($q) use ($search) {
                    $q->where('ma_tai_san', 'like', "%$search%")
                        ->orWhere('ten_tai_san', 'like', "%$search%");
                });
            })
            ->when($request->phong_id, function ($query, $phong_id) {
                $query->where('phong_id', $phong_id);
            })
            ->paginate(6);

        return view('taisan.index', compact('listTaiSan', 'phongs'));
    }

    /** ➕ Form thêm tài sản vào phòng */
    public function create()
    {
        $phongs = Phong::all();
        $khoTaiSans = KhoTaiSan::where('so_luong', '>', 0)
            ->orderBy('ten_tai_san')
            ->get(); // chỉ lấy tài sản còn trong kho
        return view('taisan.create', compact('phongs', 'khoTaiSans'));
    }

    /** 💾 Lưu tài sản phòng và trừ kho */
    public function store(Request $request)
    {
        $request->validate([
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'phong_id' => 'required|exists:phong,id',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
        ]);

        $kho = KhoTaiSan::findOrFail($request->kho_tai_san_id);

        if ($kho->so_luong < $request->so_luong) {
            return back()->with('error', 'Số lượng trong kho không đủ!');
        }

        TaiSan::create([
            'kho_tai_san_id' => $kho->id,
            'ten_tai_san' => $kho->ten_tai_san,
            'phong_id' => $request->phong_id,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'hinh_anh' => 'taisan/' . $kho->hinh_anh,
        ]);

        $kho->decrement('so_luong', $request->so_luong);

        return redirect()->route('taisan.index')->with('success', 'Thêm tài sản thành công và trừ kho!');
    }

    /** ✏️ Form chỉnh sửa tài sản phòng */
    public function edit($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $phongs = Phong::all();
        $khoTaiSans = KhoTaiSan::orderBy('ten_tai_san')->get();
        return view('taisan.edit', compact('taiSan', 'phongs', 'khoTaiSans'));
    }

    /** 🔄 Cập nhật tài sản phòng */
    /** 🔄 Cập nhật tài sản phòng */
    public function update(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);

        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'phong_id' => 'required|exists:phong,id',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
        ]);

        $kho = KhoTaiSan::findOrFail($request->kho_tai_san_id);
        $chenhLech = $request->so_luong - $taiSan->so_luong;

        // Kiểm tra tồn kho khi tăng số lượng
        if ($chenhLech > 0 && $kho->so_luong < $chenhLech) {
            return back()->with('error', 'Không đủ số lượng trong kho để tăng tài sản!');
        }

        // Cập nhật lại kho nếu số lượng thay đổi
        if ($chenhLech > 0) {
            $kho->decrement('so_luong', $chenhLech);
        } elseif ($chenhLech < 0) {
            $kho->increment('so_luong', abs($chenhLech));
        }

        // Cập nhật thông tin tài sản
        $taiSan->update([
            'kho_tai_san_id' => $kho->id,
            'ten_tai_san' => $kho->ten_tai_san,
            'phong_id' => $request->phong_id,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
        ]);

        return redirect()->route('taisan.index')->with('success', 'Cập nhật tài sản thành công!');
    }

    public function destroy($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $kho = KhoTaiSan::find($taiSan->kho_tai_san_id);

        if ($kho) {
            $kho->increment('so_luong', $taiSan->so_luong);
        }

        $taiSan->delete();

        return redirect()->route('taisan.index')->with('success', 'Đã xóa tài sản và hoàn kho thành công!');
    }

    /** 🖼️ Modal xem chi tiết */
    public function showModal($id)
    {
        $taiSan = TaiSan::with(['phong', 'khoTaiSan'])->find($id);
        if (!$taiSan) {
            return response()->json(['data' => '<p class="text-danger">Không tìm thấy tài sản.</p>']);
        }

        $html = view('taisan._modal', compact('taiSan'))->render();
        return response()->json(['data' => $html]);
    }
}
