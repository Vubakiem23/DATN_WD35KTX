<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HoaDon;
use App\Exports\HoaDonExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HoaDonDienNuocImport;

use Illuminate\Http\Request;



class HoaDonController extends Controller
{

public function importHoaDon(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(new HoaDonDienNuocImport, $request->file('file'));

    return back()->with('success', 'Nhập hóa đơn điện nước thành công!');
}

public function index()
{
    $hoaDons = HoaDon::with('phong')->get();
    return view('hoadon.index', compact('hoaDons'));
}
public function destroy($id)
{
    $hoaDon = HoaDon::find($id);

    if (!$hoaDon) {
        return redirect()->back()->with('error', 'Không tìm thấy hóa đơn!');
    }

    $hoaDon->delete();

    return redirect()->back()->with('success', 'Xóa hóa đơn thành công!');
}
public function exportPDF($id)
{
    $hoaDon = HoaDon::with('phong')->findOrFail($id);
    $pdf = Pdf::loadView('hoadon.pdf', compact('hoaDon'))
              ->setPaper('A4', 'portrait');

    return $pdf->download('hoa_don_phong_'.$hoaDon->phong->ten_phong.'.pdf');
}
public function exportExcelPhong($id)
{
    $hoaDon = HoaDon::findOrFail($id);

    // Tạo tên file: Hóa đơn phòng [Tên phòng].xlsx
    $tenPhong = $hoaDon->phong->ten_phong ?? 'Khong_ro';
    $fileName = 'HoaDon_Phong_' . $tenPhong . '.xlsx';

    // Truyền dữ liệu sang export
    return Excel::download(new HoaDonExport($hoaDon), $fileName);
}


}
