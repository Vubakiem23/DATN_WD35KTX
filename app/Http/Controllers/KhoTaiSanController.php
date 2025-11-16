<?php

namespace App\Http\Controllers;

use App\Models\LoaiTaiSan;
use App\Models\KhoTaiSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KhoTaiSanController extends Controller

{
    /** 🧱 Trang kho: hiển thị danh sách loại tài sản */
    public function index(Request $request)
    {
        // Lấy danh sách loại tài sản cho dropdown
        $tatCaLoai = LoaiTaiSan::all();

        // Tạo query cơ bản
        $query = LoaiTaiSan::withSum('khoTaiSan', 'so_luong');

        // Lọc theo loại tài sản
        if ($request->filled('loai_id')) {
            $query->where('id', $request->loai_id);
        }

        // Lọc theo tình trạng
        if ($request->filled('tinh_trang')) {
            $query->whereHas('khoTaiSan', function ($q) use ($request) {
                $q->where('tinh_trang', $request->tinh_trang);
            });
        }

        // Lọc theo từ khóa (tên loại)
        if ($request->filled('keyword')) {
            $query->where('ten_loai', 'like', '%' . $request->keyword . '%');
        }

        // Phân trang
        $loaiTaiSan = $query->orderBy('id', 'desc')->paginate(8);

        return view('kho.index', compact('loaiTaiSan', 'tatCaLoai'));
    }


    /** 🔁 Hiển thị các tài sản cùng loại */
    public function related(Request $request, $loai_id)
{
    $loai = LoaiTaiSan::findOrFail($loai_id);

    // Lấy query ban đầu
    $query = KhoTaiSan::with(['phong', 'taiSans.phong'])
        ->where('loai_id', $loai_id);

    // Lọc theo tình trạng nếu có
    if ($request->filled('tinh_trang')) {
        $query->where('tinh_trang', $request->tinh_trang);
    }

    // Lọc theo mã tài sản nếu có
    if ($request->filled('ma_tai_san')) {
        $query->where('ma_tai_san', 'like', '%' . $request->ma_tai_san . '%');
    }

    // Lấy toàn bộ kết quả trước khi phân trang
    $taiSanCollection = $query->orderBy('id', 'desc')->get();

    // Sắp xếp: chưa gán phòng lên đầu, đã gán phòng xuống cuối
    $taiSanCollection = $taiSanCollection->sortBy(function ($item) {
        return $item->taiSans->whereNotNull('phong_id')->count() > 0 ? 1 : 0;
    })->values();

    // Phân trang thủ công
    $perPage = 5;
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $taiSanCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

    $taiSan = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $taiSanCollection->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('kho.related', compact('loai', 'taiSan'));
}

    public function create($loai_id)
    {
        $loai = LoaiTaiSan::findOrFail($loai_id);
        $tinhTrangOptions = ['Mới', 'Hỏng', 'Cũ', 'Bảo trì', 'Bình thường'];
        return view('kho.create', compact('loai', 'tinhTrangOptions'));
    }


    public function store(Request $request, $loai_id)
    {
        $loai = LoaiTaiSan::findOrFail($loai_id);

        // ✅ Validate mảng dữ liệu
        $request->validate([
            'ten_tai_san.*' => 'required|string|max:255',
            'don_vi_tinh.*' => 'nullable|string|max:50',
            'tinh_trang.*' => 'nullable|in:Mới,Hỏng,Cũ,Bảo trì,Bình thường',
            'ghi_chu.*' => 'nullable|string',
            'hinh_anh.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $count = count($request->ten_tai_san);

        for ($i = 0; $i < $count; $i++) {
            $path = null;

            if ($request->hasFile("hinh_anh.$i")) {
                $path = $request->file("hinh_anh.$i")->store('kho', 'public');
            }

            KhoTaiSan::create([
                'ma_tai_san' => $this->generateMaTaiSan(),
                'loai_id' => $loai->id,
                'ten_tai_san' => $request->ten_tai_san[$i],
                'so_luong' => 1, // mỗi dòng 1 tài sản
                'don_vi_tinh' => $request->don_vi_tinh[$i] ?? null,
                'tinh_trang' => $request->tinh_trang[$i] ?? null,
                'ghi_chu' => $request->ghi_chu[$i] ?? null,
                'hinh_anh' => $path,
            ]);
        }

        return redirect()->route('kho.related', $loai_id)
            ->with('success', "Đã thêm $count tài sản cho loại {$loai->ten_loai}!");
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
            'so_luong' => 'nullable|integer|min:0',
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
            'so_luong' => $request->so_luong ?? 1,
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
        DB::beginTransaction();
        try {
            $taiSan = KhoTaiSan::findOrFail($id);

            $loai_id = $taiSan->loai_id;
            $hinhAnh = $taiSan->hinh_anh; // Lưu lại đường dẫn ảnh trước khi xóa

            // 🔹 Kiểm tra xem ảnh này còn được dùng ở nơi khác không
            $anhDangDung = KhoTaiSan::where('hinh_anh', $hinhAnh)
                ->where('id', '!=', $taiSan->id)
                ->exists();

            // Xóa bản ghi
            $taiSan->delete();

            // Xóa file ảnh sau khi đã xóa bản ghi thành công
            if (!$anhDangDung && $hinhAnh && Storage::disk('public')->exists($hinhAnh)) {
                // Chỉ xóa file nếu không ai khác đang dùng nó
                Storage::disk('public')->delete($hinhAnh);
            }

            DB::commit();

            return redirect()->route('kho.related', $loai_id)
                ->with('success', 'Đã xóa tài sản khỏi kho!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa tài sản khỏi kho: ' . $e->getMessage(), [
                'tai_san_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi xóa tài sản khỏi kho!');
        }
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
