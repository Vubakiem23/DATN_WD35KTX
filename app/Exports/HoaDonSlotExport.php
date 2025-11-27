<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HoaDonSlotExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                'Phòng' => $item->hoaDon->phong->ten_phong ?? '',
                'Mã sinh viên' => $item->sinhVien->ma_sinh_vien ?? '',
                'Tên sinh viên' => $item->sinh_vien_ten,
                'Slot' => $item->slot_label,
                'Trạng thái' => $item->trang_thai == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán',
                'Ngày tạo' => $item->created_at->format('d/m/Y H:i'),
                'Ngày thanh toán' => $item->ngay_thanh_toan ? $item->ngay_thanh_toan->format('d/m/Y H:i') : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Phòng',
            'Mã sinh viên',
            'Tên sinh viên',
            'Slot',
            'Trạng thái',
            'Ngày tạo',
            'Ngày thanh toán',
        ];
    }
}
