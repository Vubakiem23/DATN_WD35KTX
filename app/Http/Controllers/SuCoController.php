<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Http\Request;

class SuCoController extends Controller
{
    // Danh sách sự cố
    public function index()
    {
        $suco = SuCo::with(['sinhVien', 'phong'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.su_co.index', compact('suco'));
    }

    // Form thêm mới (Báo sự cố)
    public function create()
    {
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('admin.su_co.create', compact('sinhviens', 'phongs'));
    }

    // Lưu sự cố mới
    public function store(Request $request)
    {
        $request->validate([
            'sinh_vien_id' => 'required|exists:sinh_vien,id',
            'phong_id' => 'required|exists:phong,id',
            'mo_ta' => 'required|string|max:1000',
        ]);

        SuCo::create([
            'sinh_vien_id' => $request->sinh_vien_id,
            'phong_id' => $request->phong_id,
            'mo_ta' => $request->mo_ta,
            'ngay_gui' => now(),
            'trang_thai' => 'Tiếp nhận',
        ]);

        return redirect()->route('suco.index')->with('success', 'Đã thêm sự cố mới thành công!');
    }

    // Xem chi tiết
    public function show($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        return view('admin.su_co.show', compact('suco'));
    }

    // Form sửa / cập nhật trạng thái
    public function edit($id)
    {
        $suco = SuCo::findOrFail($id);
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('admin.su_co.edit', compact('suco', 'sinhviens', 'phongs'));
    }

    // Cập nhật sự cố
    public function update(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string',
        ]);

        $suco = SuCo::findOrFail($id);
        $suco->update([
            'trang_thai' => $request->trang_thai,
        ]);

        return redirect()->route('suco.index')->with('success', 'Cập nhật trạng thái sự cố thành công!');
    }

    // Xóa sự cố
    public function destroy($id)
    {
        $suco = SuCo::findOrFail($id);
        $suco->delete();

        return redirect()->route('suco.index')->with('success', 'Xóa sự cố thành công!');
    }
}
