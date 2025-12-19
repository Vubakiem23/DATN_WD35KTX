<?php

namespace App\Http\Controllers;

use App\Models\PhanHoiSinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanHoiSinhVienController extends Controller
{
    public function list()
    {
        $user = Auth::user();
        $phanHois = PhanHoiSinhVien::where('sinh_vien_id', $user->id)->orderByDesc('id')->get();
        return view('public.phan_hoi.list', compact('phanHois'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $phanHoi = PhanHoiSinhVien::where('sinh_vien_id', $user->id)->where('id', $id)->first();
        if (!$phanHoi) {
            return redirect()->route('client.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
        }
        return view('public.phan_hoi.show', compact('phanHoi'));
    }

    public function create()
    {
        return view('public.phan_hoi.create');
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $tieu_de = $request->input('tieu_de');
            $noi_dung = $request->input('noi_dung');

            $phanHoi = new PhanHoiSinhVien();
            $phanHoi->sinh_vien_id = $user->id;
            $phanHoi->tieu_de = $tieu_de;
            $phanHoi->noi_dung = $noi_dung;
            $phanHoi->save();

            return redirect()->route('client.phan_hoi.list')->with('success', 'Đã gửi phản hồi thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        try {
            $user = Auth::user();

            $phanHoi = PhanHoiSinhVien::where('sinh_vien_id', $user->id)->where('id', $id)->first();
            if (!$phanHoi) {
                return redirect()->route('client.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
            }

            $tieu_de = $request->input('tieu_de');
            $noi_dung = $request->input('noi_dung');

            $phanHoi->tieu_de = $tieu_de;
            $phanHoi->noi_dung = $noi_dung;
            $phanHoi->save();

            return redirect(route('client.phan_hoi.list'))->with('success', 'Chỉnh sửa phản hồi thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function delete($id, Request $request)
    {
        try {
            $user = Auth::user();

            $phanHoi = PhanHoiSinhVien::where('sinh_vien_id', $user->id)->where('id', $id)->first();
            if (!$phanHoi) {
                return redirect()->route('client.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
            }
            $phanHoi->delete();

            return redirect()->route('client.phan_hoi.list')->with('success', 'Đã xóa phản hồi thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
