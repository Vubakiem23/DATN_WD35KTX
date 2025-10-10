<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhongRequest;
use App\Models\Phong;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // nếu dev muốn mở, có thể tạm comment
    }

    /**
     * Hiển thị danh sách phòng với filter/pagination
     */
    public function index(Request $request)
    {
        $q = Phong::query();

        if ($request->filled('khu')) $q->where('khu', $request->khu);
        if ($request->filled('loai_phong')) $q->where('loai_phong', $request->loai_phong);
        if ($request->filled('gioi_tinh')) $q->where('gioi_tinh', $request->gioi_tinh);
        if ($request->filled('trang_thai')) $q->where('trang_thai', $request->trang_thai);
        if ($request->filled('search')) $q->where('ten_phong', 'like', '%' . $request->search . '%');

        $phongs = $q->orderBy('khu')->orderBy('ten_phong')->paginate(15);

        $totals = [
            'total' => Phong::count(),
            'trong' => Phong::where('trang_thai', 'Trống')->count(),
            'da_o' => Phong::where('trang_thai', 'Đã ở')->count(),
            'bao_tri' => Phong::where('trang_thai', 'Bảo trì')->count(),
        ];

        return view('phong.index', compact('phongs', 'totals'));
    }

    public function create()
    {
        return view('phong.create');
    }

    public function store(PhongRequest $request)
    {
        Phong::create($request->validated());
        return redirect()->route('phong.index')->with('status', 'Thêm phòng thành công');
    }

    public function edit(Phong $phong)
    {
        return view('phong.edit', compact('phong'));
    }

    public function update(PhongRequest $request, Phong $phong)
    {
        $phong->update($request->validated());
        $phong->updateStatusBasedOnCapacity();
        return redirect()->route('phong.index')->with('status', 'Cập nhật phòng thành công');
    }

    public function destroy(Phong $phong)
    {
        if ($phong->sinhviens()->where('trang_thai_ho_so', 'Đã duyệt')->count() > 0) {
            return redirect()->route('phong.index')->with('error', 'Không thể xóa: còn sinh viên đã duyệt trong phòng');
        }
        $phong->delete();
        return redirect()->route('phong.index')->with('status', 'Xóa phòng thành công');
    }

    /**
     * AJAX đổi trạng thái nhanh (POST)
     */
    public function changeStatus(Request $request, Phong $phong)
    {
        $request->validate(['trang_thai' => 'required|in:Trống,Đã ở,Bảo trì']);
        $phong->trang_thai = $request->trang_thai;
        $phong->save();
        return response()->json(['ok' => true, 'trang_thai' => $phong->trang_thai]);
    }
}
