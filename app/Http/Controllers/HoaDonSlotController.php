<?php

namespace App\Http\Controllers;

use App\Models\HoaDonSlotPayment;
use App\Exports\HoaDonSlotExport;
use Maatwebsite\Excel\Facades\Excel;

class HoaDonSlotController extends Controller
{
    public function exportAll()
    {
        $data = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien'])->get();
        return Excel::download(new HoaDonSlotExport($data), 'hoa_don_slot_all.xlsx');
    }

    public function exportPaid()
    {
        $data = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien'])
            ->where('trang_thai', HoaDonSlotPayment::TRANG_THAI_DA_THANH_TOAN)
            ->get();

        return Excel::download(new HoaDonSlotExport($data), 'hoa_don_slot_paid.xlsx');
    }

    public function exportUnpaid()
    {
        $data = HoaDonSlotPayment::with(['hoaDon.phong', 'sinhVien'])
            ->where('trang_thai', HoaDonSlotPayment::TRANG_THAI_CHUA_THANH_TOAN)
            ->get();

        return Excel::download(new HoaDonSlotExport($data), 'hoa_don_slot_unpaid.xlsx');
    }
    
}
