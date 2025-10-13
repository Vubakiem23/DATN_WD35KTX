<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $listTaiSan = $query->orderBy('tai_san.id', 'desc')->get();
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

        ]);

        DB::table('tai_san')->insert([
            'ten_tai_san' => $request->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,

            'phong_id' => $request->phong_id ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('taisan.index')->with('status', 'Thêm tài sản thành công!');
    }

    // Hiển thị form edit (chỉ 1 lần)
    public function edit($id)
    {
        $taiSan = DB::table('tai_san')->where('id', $id)->first();
        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Tài sản không tồn tại!');
        }

        $phongs = DB::table('phong')->select('id', 'ten_phong')->get();

        return view('taisan.edit', compact('taiSan', 'phongs'));
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ten_tai_san' => 'required|string|max:255',
            'so_luong' => 'required|integer|min:0',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',

        ]);

        $updated = DB::table('tai_san')->where('id', $id)->update([
            'ten_tai_san' => $request->ten_tai_san,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,

            'phong_id' => $request->phong_id ?: null,
            'updated_at' => now(),
        ]);

        if ($updated) {
            return redirect()->route('taisan.index')->with('status', 'Cập nhật tài sản thành công!');
        }

        return redirect()->back()->with('error', 'Không thể cập nhật tài sản!');
    }

    // Xóa
    public function destroy($id)
    {
        $taiSan = DB::table('tai_san')->where('id', $id)->first();
        if (!$taiSan) {
            return redirect()->route('taisan.index')->with('error', 'Tài sản không tồn tại!');
        }

        DB::table('tai_san')->where('id', $id)->delete();

        return redirect()->route('taisan.index')->with('status', 'Xóa tài sản thành công!');
    }
    public function baoHong($id)
{
    // Kiểm tra xem tài sản có tồn tại không
    $taiSan = DB::table('tai_san')->where('id', $id)->first();

    if (!$taiSan) {
        return redirect()->route('taisan.index')->with('error', 'Không tìm thấy tài sản!');
    }

    // Cập nhật tình trạng hiện tại thành "đã hỏng"
    DB::table('tai_san')->where('id', $id)->update([
        'tinh_trang_hien_tai' => 'đã hỏng',
        'updated_at' => now(),
    ]);

    return redirect()->route('taisan.index')->with('status', 'Đã báo hỏng tài sản thành công!');
}

}
