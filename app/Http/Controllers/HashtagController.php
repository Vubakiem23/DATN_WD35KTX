<?php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // Hiển thị danh sách hashtag
    public function index()
    {
        $hashtags = Hashtag::orderBy('id', 'desc')->paginate(10);
        return view('hashtags.index', compact('hashtags'));
    }

    // Form thêm mới
    public function create()
    {
        return view('hashtags.create');
    }

    // Xử lý thêm mới
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|unique:hashtags,ten|max:255',
        ]);

        Hashtag::create($request->only('ten'));
        return redirect()->route('hashtags.index')->with('success', 'Thêm hashtag thành công!');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $hashtag = Hashtag::findOrFail($id);
        return view('hashtags.edit', compact('hashtag'));
    }

    // Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $hashtag = Hashtag::findOrFail($id);

        $request->validate([
            'ten' => 'required|max:255|unique:hashtags,ten,' . $id,
        ]);

        $hashtag->update($request->only('ten'));
        return redirect()->route('hashtags.index')->with('success', 'Cập nhật hashtag thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $hashtag = Hashtag::findOrFail($id);
        $hashtag->delete();

        return redirect()->route('hashtags.index')->with('success', 'Xóa hashtag thành công!');
    }
}
