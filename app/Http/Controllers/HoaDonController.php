<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\HoaDon;
use App\Exports\HoaDonExport;
use App\Models\Phong;
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

    public function index(Request $request)
    {
        $trangThai = $request->get('trang_thai');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');


        $giaPhongMin = $request->get('gia_phong_min');
        $giaPhongMax = $request->get('gia_phong_max');

        $khu = $request->get('khu');
        $phongId = $request->get('phong_id');



        $hoaDons = HoaDon::with(['phong.khu'])
    ->when($khu, function ($query) use ($khu) {
        $query->whereHas('phong.khu', function ($q) use ($khu) {
            $q->where('ten_khu', $khu); // hoặc where('id', $khu) nếu lọc theo ID
        });
            })
            ->when($phongId, function ($query) use ($phongId) {
                $query->where('phong_id', $phongId);
            })
            ->when($trangThai === 'da_thanh_toan', fn($q) => $q->where('da_thanh_toan', true))
            ->when($trangThai === 'chua_thanh_toan', fn($q) => $q->where('da_thanh_toan', false))
            ->when($giaPhongMin, fn($q) => $q->whereHas('phong', fn($q) => $q->where('gia_phong', '>=', $giaPhongMin)))
            ->when($giaPhongMax, fn($q) => $q->whereHas('phong', fn($q) => $q->where('gia_phong', '<=', $giaPhongMax)))
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))

            ->get()
            ->map(function ($hoaDon) {
                $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
                $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

                $hoaDon->khoang_thoi_gian = ($hoaDon->created_at ? $hoaDon->created_at->format('d/m/Y') : '-') . ' → ' .
                    ($hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-');

                // Kiểm tra phong trước khi truy cập
                $giaPhong = optional($hoaDon->phong)->gia_phong ?? 0;

                $hoaDon->gia_phong = $giaPhong;
                $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
                $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
                $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $giaPhong;

                return $hoaDon;
            });

        $dsPhongs = Phong::all();
        return view('hoadon.index', compact('hoaDons', 'dsPhongs'));
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

        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->phong->gia_phong;

        $pdf = Pdf::loadView('hoadon.pdf', compact('hoaDon'));
        return $pdf->stream('hoa-don-' . $hoaDon->id . '.pdf');
    }
    public function export(Request $request)
    {
        $trangThai = $request->get('trang_thai'); // lọc theo trạng thái nếu có
        $fileName = 'DanhSach_HoaDon.xlsx';

        return Excel::download(new HoaDonExport($trangThai), $fileName);
    }


    public function thanhToan($id, Request $request)
    {
        $hoaDon = HoaDon::findOrFail($id);
        $hoaDon->trang_thai = 'Đã thanh toán';
        $hoaDon->da_thanh_toan = true;
        $hoaDon->ngay_thanh_toan = now();
        $hoaDon->hinh_thuc_thanh_toan = $request->hinh_thuc_thanh_toan;
        $request->validate(['ghi_chu_thanh_toan' => 'required|string|max:255',]);
        $hoaDon->ghi_chu_thanh_toan = $request->ghi_chu_thanh_toan;

        $hoaDon->save();

        $bienLaiHtml = $this->hienThiBienLai($hoaDon);

    return response()->json([
        'success' => true,
        'bien_lai' => $bienLaiHtml,
    ]);
    }

    public function show($id)
    {
        $hoaDon = HoaDon::with('phong')->findOrFail($id);

        // Tính toán lại nếu cần
        $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
        $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;

        $hoaDon->tien_dien = $so_dien * $hoaDon->don_gia_dien;
        $hoaDon->tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
        $hoaDon->thanh_tien = $hoaDon->tien_dien + $hoaDon->tien_nuoc + $hoaDon->phong->gia_phong;

        return view('hoadon.show', compact('hoaDon'));
    }
    public function edit($id)
    {
        $hoaDon = HoaDon::with('phong')->findOrFail($id);
        return view('hoadon.edit', compact('hoaDon'));
    }
    public function update(Request $request, $id)
{
    $hoaDon = HoaDon::findOrFail($id);

    // Cập nhật đơn giá
    $hoaDon->don_gia_dien = $request->don_gia_dien;
    $hoaDon->don_gia_nuoc = $request->don_gia_nuoc;
    // Tính lại tiền
    $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
$so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
$gia_phong = optional($hoaDon->phong)->gia_phong ?? 0;

$hoaDon->thanh_tien = ($so_dien * $hoaDon->don_gia_dien) + ($so_nuoc * $hoaDon->don_gia_nuoc) + $gia_phong;


    $hoaDon->save();

    return redirect()->route('hoadon.index')->with('success', 'Hóa đơn đã được cập nhật!');
}

    public function lichSu()
    {
        // Lấy danh sách hóa đơn đã thanh toán, kèm thông tin phòng
        $hoaDons = HoaDon::with('phong')
            ->where('da_thanh_toan', true)
            ->orderByDesc('ngay_thanh_toan')
            ->paginate(10); // 👉 dùng phân trang để khớp với view

        return view('hoadon.lichsu', compact('hoaDons'));
    }
    public function xemBienLai($id)
{
    $hoaDon = HoaDon::with('phong')->findOrFail($id);

    if (!$hoaDon->da_thanh_toan) {
        return redirect()->route('hoadon.index')->with('error', 'Hóa đơn chưa thanh toán.');
    }

    return view('hoadon.receipt', compact('hoaDon'));
}
public function hienThiBienLai(HoaDon $hoaDon)
{
    return view('hoadon.receipt', compact('hoaDon'))->render();
}

}
