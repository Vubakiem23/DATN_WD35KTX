<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaiSanController extends Controller
{
    // Trang danh sách + lọc tài sản
    public function index(Request $request)
    {
        $query = DB::table('tai_san')
            ->leftJoin('phong', 'tai_san.phong_id', '=', 'phong.id')
            ->select('tai_san.*', 'phong.ten_phong');

        // Lọc theo tên tài sản
        if ($request->has('search') && $request->search != '') {
            $query->where('tai_san.ten_tai_san', 'like', '%' . $request->search . '%');
        }

        // Lọc theo tình trạng
        if ($request->has('tinhtrang') && $request->tinhtrang != '') {
            $query->where('tai_san.tinh_trang', 'like', '%' . $request->tinhtrang . '%');
        }

        // Phân trang
        $listTaiSan = $query->orderBy('tai_san.id', 'desc')->paginate(5);

        $totals = [
            'total' => DB::table('tai_san')->count(),
            'moi' => DB::table('tai_san')->where('tinh_trang_hien_tai', 'mới')->count(),
            'cu' => DB::table('tai_san')->where('tinh_trang_hien_tai', 'cũ')->count(),
            'baotri' => DB::table('tai_san')->where('tinh_trang_hien_tai', 'bảo trì')->count(),
            'hong' => DB::table('tai_san')->where('tinh_trang_hien_tai', 'đã hỏng')->count(),
        ];

        return view('taisan.index', compact('listTaiSan', 'totals'));
    }

    // Hiển thị form tạo
    public function create()
    {
        $phongs = DB::table('phong')->select('id', 'ten_phong')->get();
        return view('taisan.create', compact('phongs'));
    }

    // Lưu tài sản mới
    public function store(Request $request)
    {
        $this->validate($request, [
            'ten_tai_san' => 'required|string|max:255',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Xử lý ảnh (nếu có)
        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $hinhAnhPath = $request->file('hinh_anh')->store('taisans', 'public');
        }

        DB::table('tai_san')->insert([
            'ten_tai_san' => $request->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
            'hinh_anh' => $hinhAnhPath, // ✅ thêm ảnh vào DB
            'phong_id' => $request->phong_id ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('taisan.index')->with('status', 'Thêm tài sản thành công!');
    }

    // Hiển thị form edit
    public function edit($id)
    {
        $taiSan = DB::table('tai_san')->where('id', $id)->first();
        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Tài sản không tồn tại!');
        }

        $phongs = DB::table('phong')->select('id', 'ten_phong')->get();

        return view('taisan.edit', compact('taiSan', 'phongs'));
    }

    // Cập nhật tài sản
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ten_tai_san' => 'required|string|max:255',
            'so_luong' => 'required|integer|min:0',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $taiSan = DB::table('tai_san')->where('id', $id)->first();

        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Không tìm thấy tài sản!');
        }

        $data = [
            'ten_tai_san' => $request->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
            'phong_id' => $request->phong_id ?: null,
            'updated_at' => now(),
        ];

        // Nếu có ảnh mới → xóa ảnh cũ + lưu mới
        if ($request->hasFile('hinh_anh')) {
            if ($taiSan->hinh_anh && Storage::disk('public')->exists($taiSan->hinh_anh)) {
                Storage::disk('public')->delete($taiSan->hinh_anh);
            }
            $data['hinh_anh'] = $request->file('hinh_anh')->store('taisans', 'public');
        }

        DB::table('tai_san')->where('id', $id)->update($data);

        return redirect()->route('taisan.index')->with('status', 'Cập nhật tài sản thành công!');
    }

    // Xóa tài sản
    public function destroy($id)
    {
        $taiSan = DB::table('tai_san')->where('id', $id)->first();

        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Tài sản không tồn tại!');
        }

        // Xóa ảnh vật lý (nếu có)
        if ($taiSan->hinh_anh && Storage::disk('public')->exists($taiSan->hinh_anh)) {
            Storage::disk('public')->delete($taiSan->hinh_anh);
        }

        DB::table('tai_san')->where('id', $id)->delete();

        return redirect()->route('taisan.index')->with('status', 'Xóa tài sản thành công!');
    }

    // Báo hỏng tài sản
    public function baoHong($id)
    {
        $taiSan = DB::table('tai_san')->where('id', $id)->first();

        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Không tìm thấy tài sản!');
        }

        DB::table('tai_san')->where('id', $id)->update([
            'tinh_trang_hien_tai' => 'đã hỏng',
            'updated_at' => now(),
        ]);

        return redirect()->route('taisan.index')->with('status', 'Đã báo hỏng tài sản thành công!');
    }
}
