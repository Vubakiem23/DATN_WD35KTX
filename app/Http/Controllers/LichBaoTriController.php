<?php

namespace App\Http\Controllers;

use App\Models\LichBaoTri;
use App\Models\TaiSan;
use Illuminate\Http\Request;

class LichBaoTriController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\LichBaoTri::with('taiSan');

        if ($request->ten_tai_san) {
            $query->whereHas('taiSan', function ($q) use ($request) {
                $q->where('ten_tai_san', 'like', '%' . $request->ten_tai_san . '%');
            });
        }

        if ($request->trang_thai) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($request->ngay_bao_tri) {
            $query->whereDate('ngay_bao_tri', $request->ngay_bao_tri);
        }

        // 🔹 Thay get() bằng paginate()
        $lich = $query
            ->orderByRaw("CASE 
        WHEN trang_thai = 'Hoàn thành' THEN 2
        WHEN trang_thai = 'Đang bảo trì' THEN 1
        ELSE 0
    END") // Sắp trạng thái
            ->orderBy('ngay_bao_tri', 'desc') // Sau đó sắp theo ngày
            ->paginate(5);


        return view('lichbaotri.index', compact('lich'));
    }



    public function create()
    {
        $phongs = \App\Models\Phong::all(); // Lấy danh sách phòng
        $taiSan = \App\Models\TaiSan::with('phong')->get(); // Lấy danh sách tài sản
        return view('lichbaotri.create', compact('phongs', 'taiSan'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'ngay_bao_tri' => 'required|date',
            'ngay_hoan_thanh' => 'nullable|date',
            'mo_ta' => 'nullable|string',
        ]);

        $trangThai = $request->ngay_hoan_thanh ? 'Hoàn thành' : 'Đang bảo trì';

        LichBaoTri::create([
            'tai_san_id' => $request->tai_san_id,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $trangThai,
        ]);

        return redirect()->route('lichbaotri.index')
            ->with('success', 'Thêm lịch bảo trì thành công!');
    }


    public function edit($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);
        $taiSan = TaiSan::with('phong')->get();
        return view('lichbaotri.edit', compact('lichBaoTri', 'taiSan'));
    }


    public function update(Request $request, $id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        $request->validate([
            'tai_san_id' => 'required|exists:tai_san,id',
            'ngay_bao_tri' => 'required|date',
            'ngay_hoan_thanh' => 'nullable|date',
            'mo_ta' => 'nullable|string',
        ]);

        // Nếu có ngày hoàn thành thì trạng thái = Hoàn thành, ngược lại = Đang bảo trì
        $trangThai = $request->ngay_hoan_thanh ? 'Hoàn thành' : 'Đang bảo trì';

        $lichBaoTri->update([
            'tai_san_id' => $request->tai_san_id,
            'ngay_bao_tri' => $request->ngay_bao_tri,
            'ngay_hoan_thanh' => $request->ngay_hoan_thanh,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $trangThai,
        ]);

        return redirect()->route('lichbaotri.index')
            ->with('success', 'Cập nhật lịch bảo trì thành công!');
    }



    public function destroy($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);
        $lichBaoTri->delete();

        return redirect()->route('lichbaotri.index')->with('success', 'Đã xóa lịch bảo trì thành công!');
    }


    public function hoanThanh($id)
    {
        $lichBaoTri = LichBaoTri::findOrFail($id);

        $lichBaoTri->update([
            'trang_thai' => 'Hoàn thành',
            'ngay_hoan_thanh' => now()->toDateString(), // Lấy ngày hiện tại
        ]);

        return redirect()->route('lichbaotri.index')->with('success', 'Đã cập nhật trạng thái hoàn thành!');
    }
}
