<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\HoaDon;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\HoaDonMail;

class HoaDonController extends Controller
{
    // Hiển thị danh sách hóa đơn
    public function index(Request $request)
    {
        $query = HoaDon::query();

        if ($request->filled('loai_phi')) {
            $query->where('loai_phi', $request->loai_phi);
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        if ($request->filled('sinh_vien_id')) {
            $query->where('sinh_vien_id', $request->sinh_vien_id);
        }

        $hoaDons = $query->with('sinhVien')->orderBy('ngay_tao', 'desc')->paginate(10);


        return view('hoadon.index', compact('hoaDons'));
    }

    // Hiển thị form tạo hóa đơn
    public function create()
    {
        return view('hoadon.create');
    }

    // Lưu hóa đơn mới
    public function store(Request $request)
    {
        $request->validate([
            'sinh_vien_id' => 'required|integer',
            'loai_phi' => 'required|string',
            'so_tien' => 'required|numeric',
            'ngay_tao' => 'required|date',
            'trang_thai' => 'required|string',
        ]);

        HoaDon::create($request->all());

        return redirect()->route('hoadon.index')->with('success', 'Đã tạo hóa đơn mới!');
    }

    // Hiển thị form sửa hóa đơn
    public function edit($id)
    {
        $hoaDon = HoaDon::findOrFail($id);
        return view('hoadon.edit', compact('hoaDon'));
    }

    // Cập nhật hóa đơn
    public function update(Request $request, $id)
    {
        $request->validate([
            'loai_phi' => 'required|string',
            'so_tien' => 'required|numeric',
            'ngay_tao' => 'required|date',
            'trang_thai' => 'required|string',
        ]);

        $hoaDon = HoaDon::findOrFail($id);
        $hoaDon->update($request->only(['loai_phi', 'so_tien', 'ngay_tao', 'trang_thai']));

        return redirect()->route('hoadon.index')->with('success', 'Cập nhật hóa đơn thành công!');
    }

    // Xóa hóa đơn
    public function destroy($id)
    {
        $hoaDon = HoaDon::findOrFail($id);
        $hoaDon->delete();

        return redirect()->route('hoadon.index')->with('success', 'Đã xóa hóa đơn.');
    }

    // Sao chép hóa đơn
    public function duplicate($id)
    {
        $hoaDon = HoaDon::findOrFail($id);
        $newHoaDon = $hoaDon->replicate();
        $newHoaDon->created_at = now();
        $newHoaDon->updated_at = now();
        $newHoaDon->save();

        return redirect()->route('hoadon.index')->with('success', 'Đã sao chép hóa đơn.');
    }


    public function exportPDF($id)
    {
        $hoaDon = HoaDon::findOrFail($id);
        $pdf = PDF::loadView('hoadon.pdf', compact('hoaDon'));

        return $pdf->download('hoadon_' . $hoaDon->id . '.pdf');
    }

    public function pay($id)
    {
        $hoaDon = HoaDon::findOrFail($id);
        $hoaDon->trang_thai = 'Đã thanh toán';
        $hoaDon->save();

        return back()->with('success', 'Hóa đơn đã được thanh toán.');
    }
    public function history()
{
    $hoaDons = HoaDon::with('sinhVien')
        ->where('trang_thai', 'Đã thanh toán')
        ->orderBy('ngay_thanh_toan', 'desc')
        ->paginate(10);

    return view('hoadon.history', compact('hoaDons'));
}



}
