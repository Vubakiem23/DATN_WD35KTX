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
    protected $hoaDon;

    public function __construct(HoaDon $hoaDon)
    {
        $this->hoaDon = $hoaDon;
    }

    public function collection()
    {
        $hoaDon = $this->hoaDon;

        return new Collection([
            [
                $hoaDon->phong->ten_phong ?? 'Không rõ',
                $hoaDon->so_dien_cu,
                $hoaDon->so_dien_moi,
                $hoaDon->so_dien_moi - $hoaDon->so_dien_cu,
                $hoaDon->so_nuoc_cu,
                $hoaDon->so_nuoc_moi,
                $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu,
                number_format($hoaDon->don_gia_dien, 0, ',', '.') . ' VND',
                number_format($hoaDon->don_gia_nuoc, 0, ',', '.') . ' VND',
                number_format($hoaDon->thanh_tien, 0, ',', '.') . ' VND',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Phòng',
            'Điện cũ',
            'Điện mới',
            'Đã dùng (điện)',
            'Nước cũ',
            'Nước mới',
            'Đã dùng (nước)',
            'Đơn giá điện',
            'Đơn giá nước',
            'Thành tiền',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Đặt font tổng thể
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        // Tiêu đề hàng đầu tiên (headings)
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('0070C0'); // Màu xanh dương header

        // Căn giữa tiêu đề
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal('center')->setVertical('center');

        // Căn giữa toàn bộ dữ liệu
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal('center')->setVertical('center');

        // Thêm border cho toàn bảng
        $sheet->getStyle('A1:J2')->getBorders()->getAllBorders()->setBorderStyle('thin');

        // Auto width cho các cột
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Đổi màu nền nhẹ cho dòng dữ liệu
        $sheet->getStyle('A2:J2')->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('E9F3FF'); // xanh nhạt

        // Thêm tiêu đề hóa đơn trên đầu (merge ô)
        $sheet->insertNewRowBefore(1, 2); // chèn 2 dòng trống ở đầu
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'HÓA ĐƠN THANH TOÁN ĐIỆN NƯỚC');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('0070C0');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
    }

    public function title(): string
    {
        return 'Hóa đơn phòng ' . ($this->hoaDon->phong->ten_phong ?? '');
    }
}
