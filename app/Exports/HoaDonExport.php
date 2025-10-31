<?php

namespace App\Exports;

use App\Models\HoaDon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HoaDonExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $trangThai;

    public function __construct($trangThai = null)
    {
        $this->trangThai = $trangThai;
    }

    public function collection()
    {
        $hoaDons = HoaDon::with('phong')
            ->when($this->trangThai === 'da_thanh_toan', fn($q) => $q->where('da_thanh_toan', true))
            ->when($this->trangThai === 'chua_thanh_toan', fn($q) => $q->where('da_thanh_toan', false))
            ->get();

        return $hoaDons->map(function ($hd) {
            $so_dien = $hd->so_dien_moi - $hd->so_dien_cu;
            $so_nuoc = $hd->so_nuoc_moi - $hd->so_nuoc_cu;
            $gia_phong = $hd->phong->gia_phong ?? 0;
            $tien_dien = $so_dien * $hd->don_gia_dien;
            $tien_nuoc = $so_nuoc * $hd->don_gia_nuoc;
            $tong_tien = $tien_dien + $tien_nuoc + $gia_phong;

            return [
                $hd->phong->ten_phong ?? 'Không rõ',
                $hd->so_dien_cu,
                $hd->so_dien_moi,
                $so_dien,
                number_format($hd->don_gia_dien, 0, ',', '.') . ' VND',
                number_format($tien_dien, 0, ',', '.') . ' VND',
                $hd->so_nuoc_cu,
                $hd->so_nuoc_moi,
                $so_nuoc,
                number_format($hd->don_gia_nuoc, 0, ',', '.') . ' VND',
                number_format($tien_nuoc, 0, ',', '.') . ' VND',
                number_format($gia_phong, 0, ',', '.') . ' VND',
                number_format($tong_tien, 0, ',', '.') . ' VND',
                $hd->da_thanh_toan ? 'Đã thanh toán' : 'Chưa thanh toán',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Phòng',
            'Điện cũ',
            'Điện mới',
            'Số điện',
            'Đơn giá điện',
            'Thành tiền điện',
            'Nước cũ',
            'Nước mới',
            'Số nước',
            'Đơn giá nước',
            'Thành tiền nước',
            'Giá phòng',
            'Tổng tiền',
            'Trạng thái',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataRowCount = $this->collection()->count(); // ✅ số dòng dữ liệu
        $startRow = 4; // ✅ dữ liệu bắt đầu từ dòng 4
        $endRow = $startRow + $dataRowCount - 1; 
        // Chèn tiêu đề lớn ở đầu
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'DANH SÁCH HÓA ĐƠN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('0070C0');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Font tổng thể
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        // Header dòng 3
        $sheet->getStyle('A3:N3')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('000000');
        $sheet->getStyle('A3:N3')->getAlignment()->setHorizontal('center')->setVertical('center');

        // Dữ liệu từ dòng 4 trở đi
        $sheet->getStyle("A{$startRow}:N{$endRow}")->getAlignment()->setHorizontal('center')->setVertical('center');
    $sheet->getStyle("A3:N{$endRow}")->getBorders()->getAllBorders()->setBorderStyle('thin');
           // Auto width cho từng cột
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Danh sách hóa đơn';
    }
}
