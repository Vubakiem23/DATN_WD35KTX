<?php

namespace App\Exports;

use App\Models\SuCo;
use App\Models\HoaDon;
use App\Models\Slot;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DashboardExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, $this->month, 1)->endOfMonth();
        
        // Tính toán các chỉ số
        $soSuCoPhatSinh = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])->count();
        $soSuCoDaXuLy = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
            ->where('trang_thai', 'Hoàn thành')
            ->count();
        
        $tongTienThuDuoc = HoaDon::whereBetween('created_at', [$startDate, $endDate])
            ->where('da_thanh_toan', true)
            ->get()
            ->sum(function ($hoaDon) {
                $so_dien = $hoaDon->so_dien_moi - $hoaDon->so_dien_cu;
                $so_nuoc = $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu;
                $gia_phong = optional($hoaDon->phong)->gia_phong ?? 0;
                $tien_dien = $so_dien * $hoaDon->don_gia_dien;
                $tien_nuoc = $so_nuoc * $hoaDon->don_gia_nuoc;
                return $tien_dien + $tien_nuoc + $gia_phong;
            });
        
        $tongChiPhiBaoTri = SuCo::whereBetween('ngay_gui', [$startDate, $endDate])
            ->where('is_paid', true)
            ->sum('payment_amount');
        
        $tongSlot = Slot::count();
        $slotDangCoNguoiO = Slot::whereNotNull('sinh_vien_id')->count();
        $slotDangTrong = $tongSlot - $slotDangCoNguoiO;
        
        $tyLeXuLy = $soSuCoPhatSinh > 0 ? round(($soSuCoDaXuLy / $soSuCoPhatSinh) * 100, 1) : 0;
        
        return collect([
            [
                'Chỉ số',
                'Giá trị',
                'Ghi chú'
            ],
            [
                'Số sự cố phát sinh',
                $soSuCoPhatSinh,
                'Tổng số sự cố được báo cáo trong tháng'
            ],
            [
                'Số sự cố đã xử lý',
                $soSuCoDaXuLy,
                'Số sự cố đã hoàn thành'
            ],
            [
                'Tỷ lệ xử lý (%)',
                $tyLeXuLy . '%',
                'Phần trăm sự cố đã xử lý'
            ],
            [
                'Tổng tiền thu được (VND)',
                number_format($tongTienThuDuoc, 0, ',', '.'),
                'Tổng tiền từ hóa đơn đã thanh toán (phí phòng + điện + nước)'
            ],
            [
                'Tổng chi phí bảo trì (VND)',
                number_format($tongChiPhiBaoTri, 0, ',', '.'),
                'Tổng chi phí sửa chữa từ sự cố đã thanh toán'
            ],
            [
                'Số slot đang trống',
                $slotDangTrong,
                'Tổng số slot chưa có người ở'
            ],
            [
                'Số slot đang có người ở',
                $slotDangCoNguoiO,
                'Tổng số slot đã có người ở'
            ],
            [
                'Tổng số slot',
                $tongSlot,
                'Tổng số slot trong hệ thống'
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Chỉ số',
            'Giá trị',
            'Ghi chú'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Chèn tiêu đề lớn ở đầu
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ THÁNG ' . $this->month . '/' . $this->year);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('0070C0');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Font tổng thể
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
        
        // Header dòng 3
        $sheet->getStyle('A3:C3')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('000000');
        $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle('A3:C3')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E7E6E6');
        
        // Dữ liệu từ dòng 4 trở đi
        $endRow = 12; // 9 dòng dữ liệu + 1 dòng header
        $sheet->getStyle("A4:C{$endRow}")->getAlignment()->setVertical('center');
        $sheet->getStyle("A3:C{$endRow}")->getBorders()->getAllBorders()->setBorderStyle('thin');
        
        // Auto width cho từng cột
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Căn trái cho cột A và C, căn phải cho cột B
        $sheet->getStyle("A4:A{$endRow}")->getAlignment()->setHorizontal('left');
        $sheet->getStyle("B4:B{$endRow}")->getAlignment()->setHorizontal('right');
        $sheet->getStyle("C4:C{$endRow}")->getAlignment()->setHorizontal('left');
    }

    public function title(): string
    {
        return 'Báo cáo thống kê';
    }
}


