<?php

namespace App\Http\Controllers;

use App\Models\TinTuc;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TinTucController extends Controller
{
    // Hiển thị danh sách tin tức với tìm kiếm
    public function index(Request $request)
    {
        $query = TinTuc::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tieu_de', 'like', "%{$search}%")
                  ->orWhere('noi_dung', 'like', "%{$search}%");
        }

        $tintucs = $query->with('hashtags')->orderBy('created_at', 'desc')->paginate(10);
        return view('tintuc.index', compact('tintucs'));
    }

    // Form thêm tin tức
    public function create()
    {
        $hashtags = Hashtag::all();
        return view('tintuc.create', compact('hashtags'));
    }

    // Lưu tin tức mới
    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_tao' => 'required|date',
            'hashtags' => 'array',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // Tạo slug duy nhất
        $slug = $this->createUniqueSlug($request->tieu_de);

        // Xử lý upload ảnh
        $hinhAnhPath = null;
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/tintuc'), $filename);
            $hinhAnhPath = 'images/tintuc/' . $filename;
        }

        // Tạo tin tức
        $tinTuc = TinTuc::create([
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'slug' => $slug,
            'ngay_tao' => $request->ngay_tao,
            'hinh_anh' => $hinhAnhPath,
        ]);

        // Gắn hashtags
        $tinTuc->hashtags()->sync($request->hashtags ?? []);

        return redirect()->route('tintuc.index')->with('success', 'Thêm tin tức thành công!');
    }

    // Hiển thị chi tiết tin tức
    public function show($id)
    {
        $tinTuc = TinTuc::with('hashtags')->findOrFail($id);
        return view('tintuc.show', compact('tinTuc'));
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $tintuc = TinTuc::with('hashtags')->findOrFail($id);
        $hashtags = Hashtag::all();
        return view('tintuc.edit', compact('tintuc', 'hashtags'));
    }

    // Cập nhật tin tức
    public function update(Request $request, $id)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_tao' => 'required|date',
            'hashtags' => 'array',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $tintuc = TinTuc::findOrFail($id);

        // Tạo slug duy nhất
        $slug = $this->createUniqueSlug($request->tieu_de, $id);

        // Xử lý upload ảnh mới nếu có
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/tintuc'), $filename);
            $tintuc->hinh_anh = 'images/tintuc/' . $filename;
        }

        // Cập nhật dữ liệu
        $tintuc->update([
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'slug' => $slug,
            'ngay_tao' => $request->ngay_tao,
        ]);

        // Gắn hashtags
        $tintuc->hashtags()->sync($request->hashtags ?? []);

        return redirect()->route('tintuc.index')->with('success', 'Cập nhật tin tức thành công!');
    }

    // Xóa tin tức
    public function destroy($id)
    {
        $tinTuc = TinTuc::findOrFail($id);

        // Xóa quan hệ hashtags
        $tinTuc->hashtags()->detach();

        // Xóa ảnh nếu có
        if ($tinTuc->hinh_anh && file_exists(public_path($tinTuc->hinh_anh))) {
            unlink(public_path($tinTuc->hinh_anh));
        }

        $tinTuc->delete();

        return redirect()->route('tintuc.index')->with('success', 'Xóa tin tức thành công!');
    }

    // Hàm tạo slug duy nhất
    private function createUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (TinTuc::where('slug', $slug)
                      ->when($id, fn($q) => $q->where('id', '!=', $id))
                      ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    
}
