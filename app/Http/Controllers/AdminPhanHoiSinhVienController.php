<?php

namespace App\Http\Controllers;

use App\Models\PhanHoiSinhVien;
use Illuminate\Http\Request;

class AdminPhanHoiSinhVienController extends Controller
{
    public function list(Request $request)
    {
        $query = PhanHoiSinhVien::query();

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('tieu_de', 'like', "%{$keyword}%")
                  ->orWhere('noi_dung', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $phanHois = $query->orderByDesc('id')->paginate(10)->withQueryString();
        
        return view('phan_hoi.list', compact('phanHois'));
    }

    public function resolve($id)
    {
        try {
            $phanHoi = PhanHoiSinhVien::findOrFail($id);
            $phanHoi->trang_thai = 1;
            $phanHoi->save();

            return redirect()->route('admin.phan_hoi.list')->with('success', 'Đã đánh dấu phản hồi là đã xử lý.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $phanHoi = PhanHoiSinhVien::findOrFail($id);
            $phanHoi->trang_thai = 2; // 2 = Từ chối
            $phanHoi->save();

            return redirect()->route('admin.phan_hoi.list')->with('success', 'Đã từ chối phản hồi.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show($id)
    {
        $phanHoi = PhanHoiSinhVien::where('id', $id)->first();
        if (!$phanHoi) {
            return redirect()->route('admin.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
        }
        return view('phan_hoi.show', compact('phanHoi'));
    }

    public function update($id, Request $request)
    {
        try {
            $phanHoi = PhanHoiSinhVien::where('id', $id)->first();
            if (!$phanHoi) {
                return redirect()->route('admin.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
            }

            $tieu_de = $request->input('tieu_de');
            $noi_dung = $request->input('noi_dung');
            $trang_thai = $request->input('trang_thai');

            $phanHoi->tieu_de = $tieu_de;
            $phanHoi->noi_dung = $noi_dung;
            $phanHoi->trang_thai = $trang_thai;
            $phanHoi->save();

            return redirect(route('admin.phan_hoi.list'))->with('success', 'Chỉnh sửa phản hồi thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function delete($id, Request $request)
    {
        try {
            $phanHoi = PhanHoiSinhVien::where('id', $id)->first();
            if (!$phanHoi) {
                return redirect()->route('admin.phan_hoi.list')->with('error', 'Phản hồi không tồn tại.');
            }
            $phanHoi->delete();

            return redirect()->route('admin.phan_hoi.list')->with('success', 'Đã xóa phản hồi thành công.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
