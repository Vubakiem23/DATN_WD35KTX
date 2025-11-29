<?php

namespace App\Exports;

use App\Models\HoaDonSlotPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HoaDonSlotPaymentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return HoaDonSlotPayment::with(['hoaDon.phong'])
            ->get()
            ->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'Hóa đơn' => $item->hoa_don_id,
                    'Phòng' => $item->hoaDon->phong->ten_phong ?? '---',
                    'Sinh viên' => $item->sinh_vien_ten,
                    'Slot' => $item->slot_label,
                    'Trạng thái' => $item->trang_thai,
                    'Ngày thanh toán' => $item->ngay_thanh_toan ? $item->ngay_thanh_toan->format('d/m/Y H:i') : '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Hóa đơn',
            'Phòng',
            'Sinh viên',
            'Slot',
            'Trạng thái',
            'Ngày thanh toán',
        ];
    }
}
