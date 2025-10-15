<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    public function index()
    {
        $thongbaos = ThongBao::orderBy('id', 'desc')->paginate(10);
        return view('thongbao.index', compact('thongbaos'));
    }

    public function create()
    {
        return view('thongbao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|max:255',
        ]);

        ThongBao::create($request->all());

        return redirect()->route('thongbao.index')->with('success', 'Thêm thông báo thành công!');
    }

    public function show($id)
    {
        $thongbao = ThongBao::findOrFail($id);
        return view('thongbao.show', compact('thongbao'));
    }

    public function edit($id)
    {
        $thongbao = ThongBao::findOrFail($id);
        return view('thongbao.edit', compact('thongbao'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tieu_de' => 'required|max:255',
            'noi_dung' => 'required',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|max:255',
        ]);

        $thongbao = ThongBao::findOrFail($id);
        $thongbao->update($request->all());

        return redirect()->route('thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    public function destroy($id)
    {
        $thongbao = ThongBao::findOrFail($id);
        $thongbao->delete();

        return redirect()->route('thongbao.index')->with('success', 'Xóa thông báo thành công!');
    }
}
