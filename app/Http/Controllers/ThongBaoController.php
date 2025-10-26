<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThongBaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $thongbaos = ThongBao::with('phong:id,ten_phong,khu')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('thongbao.index', compact('thongbaos'));
    }

    public function create()
    {
        $phongs = Phong::orderBy('khu')->orderBy('ten_phong')->get();
        return view('thongbao.create', compact('phongs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|max:255',
            'phong_id' => 'nullable|exists:phong,id',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('anh')) {
            $data['anh'] = $request->file('anh')->store('thongbao', 'public');
        }

        ThongBao::create($data);

        return redirect()->route('thongbao.index')->with('success', 'Thêm thông báo thành công!');
    }

    public function show(ThongBao $thongbao)
    {
        $thongbao->load('phong:id,ten_phong,khu');
        return view('thongbao.show', compact('thongbao'));
    }

    public function edit(ThongBao $thongbao)
    {
        $phongs = Phong::orderBy('khu')->orderBy('ten_phong')->get();
        return view('thongbao.edit', compact('thongbao', 'phongs'));
    }

    public function update(Request $request, ThongBao $thongbao)
    {
        $data = $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|max:255',
            'phong_id' => 'nullable|exists:phong,id',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('anh')) {
            // xóa ảnh cũ nếu tồn tại
            if ($thongbao->anh && Storage::disk('public')->exists($thongbao->anh)) {
                Storage::disk('public')->delete($thongbao->anh);
            }
            $data['anh'] = $request->file('anh')->store('thongbao', 'public');
        }

        $thongbao->update($data);

        return redirect()->route('thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    public function destroy(ThongBao $thongbao)
    {
        if ($thongbao->anh && Storage::disk('public')->exists($thongbao->anh)) {
            Storage::disk('public')->delete($thongbao->anh);
        }

        $thongbao->delete();

        return redirect()->route('thongbao.index')->with('success', 'Xóa thông báo thành công!');
    }
}
