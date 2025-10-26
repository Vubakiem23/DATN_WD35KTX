<?php

namespace App\Http\Controllers;

use App\Models\LoaiTaiSan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoaiTaiSanController extends Controller
{
    // Danh sách loại tài sản
  public function index(Request $request)
{
    $query = LoaiTaiSan::orderBy('id', 'desc');

    // ✅ Lọc theo tên loại (nếu có nhập)
    if ($request->filled('keyword')) {
        $query->where('ten_loai', 'like', '%' . $request->keyword . '%');
    }

    // ✅ Phân trang 5 dòng
    $loais = $query->paginate(5);

    // Giữ lại từ khóa khi chuyển trang
    $loais->appends($request->only('keyword'));

    return view('loaitaisan.index', compact('loais'));
}

    public function create()
    {
        return view('loaitaisan.create');
    }

    // Lưu loại tài sản mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_loai' => 'required|unique:loai_tai_san,ten_loai',
            'mo_ta' => 'nullable',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'ten_loai.required' => 'Vui lòng nhập tên loại tài sản!',
            'ten_loai.unique' => 'Tên loại tài sản đã tồn tại!',
        ]);

        // Tạo mã loại tự động
        $lastId = LoaiTaiSan::max('id') ?? 0;
        $maLoai = 'LTS' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $data = $request->only(['ten_loai', 'mo_ta']);
        $data['ma_loai'] = $maLoai;

        // Xử lý hình ảnh
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/loai'), $filename);
            $data['hinh_anh'] = $filename;
        }

        LoaiTaiSan::create($data);

        return redirect()->route('loaitaisan.index')->with('success', 'Thêm loại tài sản thành công!');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $loai = LoaiTaiSan::findOrFail($id);
        return view('loaitaisan.edit', compact('loai'));
    }

    // Cập nhật loại tài sản
    public function update(Request $request, $id)
    {
        $loai = LoaiTaiSan::findOrFail($id);

        $request->validate([
            'ten_loai' => 'required|unique:loai_tai_san,ten_loai,' . $id,
            'mo_ta' => 'nullable',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'ten_loai.required' => 'Vui lòng nhập tên loại tài sản!',
            'ten_loai.unique' => 'Tên loại tài sản đã tồn tại!',
        ]);


        $data = $request->only(['ten_loai', 'mo_ta']);

        // Upload ảnh mới, xóa ảnh cũ nếu có
        if ($request->hasFile('hinh_anh')) {
            if ($loai->hinh_anh && file_exists(public_path('uploads/loai/' . $loai->hinh_anh))) {
                unlink(public_path('uploads/loai/' . $loai->hinh_anh));
            }

            $file = $request->file('hinh_anh');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/loai'), $filename);
            $data['hinh_anh'] = $filename;
        }

        $loai->update($data);

        return redirect()->route('loaitaisan.index')->with('success', 'Cập nhật loại tài sản thành công!');
    }

    // Xóa loại tài sản
    public function destroy($id)
    {
        $loai = LoaiTaiSan::findOrFail($id);

        // Xóa ảnh nếu có
        if ($loai->hinh_anh && file_exists(public_path('uploads/loai/' . $loai->hinh_anh))) {
            unlink(public_path('uploads/loai/' . $loai->hinh_anh));
        }

        $loai->delete();

        return redirect()->route('loaitaisan.index')->with('success', 'Đã xóa loại tài sản!');
    }

    // Xem chi tiết loại tài sản
    public function show($id)
    {
        $loai = LoaiTaiSan::with('taiSan')->findOrFail($id);
        return view('admin.loaitaisan.show', compact('loai'));
    }
}
