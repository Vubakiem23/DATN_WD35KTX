<?php

namespace App\Http\Controllers;

use App\Models\TaiSan;
use App\Models\KhoTaiSan;
use App\Models\Phong;
use App\Models\LoaiTaiSan;
use Illuminate\Http\Request;

class TaiSanController extends Controller
{
    /** 📋 Danh sách tài sản trong phòng */
    public function index(Request $request)
{
    // Lấy danh sách phòng để filter nếu cần
    $phongs = Phong::orderBy('ten_phong')->get();

    // Lấy danh sách tài sản
    $listTaiSan = TaiSan::with(['khoTaiSan', 'phong', 'slots.sinhVien'])
        ->when($request->search, function ($query, $search) {
            $query->whereHas('khoTaiSan', function ($q) use ($search) {
                $q->where('ma_tai_san', 'like', "%$search%")
                  ->orWhere('ten_tai_san', 'like', "%$search%");
            });
        })
        ->when($request->phong_id, function ($query, $phong_id) {
            $query->where('phong_id', $phong_id);
        })
        ->orderBy('created_at', 'desc') // ✅ mới thêm lên đầu
        ->paginate(6)
        ->withQueryString(); // giữ query string khi phân trang

    return view('taisan.index', compact('listTaiSan', 'phongs'));
}


public function create(Request $request)
{
    $phongs = Phong::orderBy('ten_phong')->get();
    $loaiTaiSans = LoaiTaiSan::orderBy('ten_loai')->get();

    $taiSans = collect();
    $selectedLoai = null;
    $selectedTaiSan = null;

    // Nếu chọn loại tài sản
    if ($request->query('loai_id')) {
        $selectedLoai = LoaiTaiSan::find($request->query('loai_id'));
        if ($selectedLoai) {
            $taiSans = KhoTaiSan::where('loai_id', $selectedLoai->id)->get();
        }
    }

    // Nếu chọn tài sản
    if ($request->query('kho_tai_san_id')) {
        $selectedTaiSan = KhoTaiSan::find($request->query('kho_tai_san_id'));

        // Chuyển path thành URL đầy đủ để hiển thị
        if ($selectedTaiSan) {
            $selectedTaiSan->hinh_anh = $selectedTaiSan->hinh_anh
                ? asset('storage/' . ltrim($selectedTaiSan->hinh_anh, '/'))
                : asset('uploads/default.png'); // ảnh mặc định nếu không có
        }
    }

    return view('taisan.create', compact('phongs', 'loaiTaiSans', 'taiSans', 'selectedLoai', 'selectedTaiSan'));
}

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
            'hinh_anh' => $kho->hinh_anh, // giữ ảnh từ kho
        ]);

        $kho->decrement('so_luong', $request->so_luong);

        return redirect()->route('taisan.index')->with('success', 'Thêm tài sản thành công và trừ kho!');
    }

    /** ✏️ Form chỉnh sửa tài sản phòng */
    public function edit($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $phongs = Phong::orderBy('ten_phong')->get();
        $khoTaiSans = KhoTaiSan::orderBy('ten_tai_san')->get();

        return view('taisan.edit', compact('taiSan', 'phongs', 'khoTaiSans'));
    }

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

        if ($chenhLech > 0 && $kho->so_luong < $chenhLech) {
            return back()->with('error', 'Không đủ số lượng trong kho để tăng tài sản!');
        }

        if ($chenhLech > 0) {
            $kho->decrement('so_luong', $chenhLech);
        } elseif ($chenhLech < 0) {
            $kho->increment('so_luong', abs($chenhLech));
        }

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

    /** 🗑️ Xóa tài sản */
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
   public function related($loai_id)
{
    $khoTaiSans = KhoTaiSan::where('loai_id', $loai_id)
        ->select('id', 'ten_tai_san', 'so_luong', 'hinh_anh', 'tinh_trang')
        ->get()
        ->map(function ($item) {
            $item->hinh_anh = $item->hinh_anh 
                ? asset('storage/' . ltrim($item->hinh_anh, '/')) 
                : asset('uploads/default.png');
            return $item;
        });

    return response()->json($khoTaiSans); // phải trả JSON
}


}
