<?php

namespace App\Http\Controllers;

use App\Models\SuCo;
use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SuCoController extends Controller
{
    // 📋 Danh sách sự cố
    public function index()
    {
        $suco = SuCo::with(['sinhVien', 'phong'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.su_co.index', compact('suco'));
    }

    // 🆕 Form thêm mới (Báo sự cố)
    public function create()
    {
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('admin.su_co.create', compact('sinhviens', 'phongs'));
    }

    // 💾 Lưu sự cố mới
    public function store(Request $request)
    {
        $request->validate([
            'sinh_vien_id' => 'required|exists:sinh_vien,id',
            'phong_id' => 'required|exists:phong,id',
            'mo_ta' => 'required|string|max:1000',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['sinh_vien_id', 'phong_id', 'mo_ta']);
        $data['ngay_gui'] = now();
        $data['trang_thai'] = 'Tiếp nhận';

        // ✅ Upload ảnh (nếu có)
        if ($request->hasFile('anh')) {
            // 🔒 Tự tạo thư mục nếu chưa có
            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'uploads/suco/' . $fileName;
        }

        SuCo::create($data);

        return redirect()->route('suco.index')->with('success', 'Đã thêm sự cố mới thành công!');
    }

    // 👁️ Xem chi tiết sự cố
    public function show($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        return view('admin.su_co.show', compact('suco'));
    }

    // ✏️ Form cập nhật sự cố
    public function edit($id)
    {
        $suco = SuCo::with(['sinhVien', 'phong'])->findOrFail($id);
        $sinhviens = SinhVien::all();
        $phongs = Phong::all();
        return view('admin.su_co.edit', compact('suco', 'sinhviens', 'phongs'));
    }

    // 🔄 Cập nhật sự cố
    public function update(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|string',
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $suco = SuCo::findOrFail($id);
        $data = ['trang_thai' => $request->trang_thai];

        // ✅ Nếu có ảnh mới, xóa ảnh cũ rồi upload ảnh mới
        if ($request->hasFile('anh')) {
            if (!empty($suco->anh) && File::exists(public_path($suco->anh))) {
                File::delete(public_path($suco->anh));
            }

            // 🔒 Tạo thư mục nếu chưa tồn tại
            $uploadPath = public_path('uploads/suco');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $file = $request->file('anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'uploads/suco/' . $fileName;
        }

        $suco->update($data);

        return redirect()->route('suco.index')->with('success', 'Cập nhật sự cố thành công!');
    }

    // 🗑️ Xóa sự cố
    public function destroy($id)
    {
        $suco = SuCo::findOrFail($id);

        // ✅ Xóa ảnh nếu tồn tại
        if (!empty($suco->anh) && File::exists(public_path($suco->anh))) {
            File::delete(public_path($suco->anh));
        }

        $suco->delete();

        return redirect()->route('suco.index')->with('success', 'Xóa sự cố thành công!');
    }
}
