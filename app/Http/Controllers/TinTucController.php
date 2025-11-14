<?php

namespace App\Http\Controllers;

use App\Models\TinTuc;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TinTucController extends Controller
{
    public function index(Request $request)
    {
        $query = TinTuc::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('tieu_de', 'like', "%$search%")
                  ->orWhere('noi_dung', 'like', "%$search%");
        }

        $tintucs = $query->with('hashtags')->orderBy('created_at', 'desc')->paginate(10);
        return view('tintuc.index', compact('tintucs'));
    }

    public function create()
    {
        $hashtags = Hashtag::all();
        return view('tintuc.create', compact('hashtags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_tao' => 'required|date',
            'hashtags' => 'array'
        ]);

        // Tạo slug duy nhất
        $slug = Str::slug($request->tieu_de);
        $originalSlug = $slug;
        $counter = 1;
        while (TinTuc::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $tinTuc = TinTuc::create([
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'slug' => $slug,
            'ngay_tao' => $request->ngay_tao,
        ]);

        // Gắn hashtags
        if ($request->hashtags) {
            $tinTuc->hashtags()->sync($request->hashtags);
        }

        return redirect()->route('tintuc.index')->with('success', 'Thêm tin tức thành công!');
    }

    public function show($id)
    {
        $tinTuc = TinTuc::with('hashtags')->findOrFail($id);
        return view('tintuc.show', compact('tinTuc'));
    }

    public function edit($id)
    {
        $tintuc = TinTuc::with('hashtags')->findOrFail($id);
        $hashtags = Hashtag::all();
        return view('tintuc.edit', compact('tintuc', 'hashtags'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_tao' => 'required|date',
            'hashtags' => 'array'
        ]);

        $tintuc = TinTuc::findOrFail($id);

        // Tạo slug duy nhất khi cập nhật
        $slug = Str::slug($request->tieu_de);
        $originalSlug = $slug;
        $counter = 1;
        while (TinTuc::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

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

    public function destroy($id)
    {
        $tinTuc = TinTuc::findOrFail($id);
        $tinTuc->hashtags()->detach();
        $tinTuc->delete();

        return redirect()->route('tintuc.index')->with('success', 'Xóa tin tức thành công!');
    }
}
