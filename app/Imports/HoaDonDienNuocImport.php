<?php

namespace App\Imports;

use App\Models\HoaDon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HoaDonDienNuocImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Kiểm tra phong_id có tồn tại và hợp lệ
        if (empty($row['phong_id']) || !is_numeric($row['phong_id'])) {
            Log::warning('Thiếu hoặc sai phong_id tại dòng: ' . json_encode($row));
            return null; // Bỏ qua dòng lỗi
        }

        // Tính toán số điện và số nước
        $so_dien = (int)$row['so_dien_moi'] - (int)$row['so_dien_cu'];
        $so_nuoc = (int)$row['so_nuoc_moi'] - (int)$row['so_nuoc_cu'];

        // Đơn giá
        $don_gia_dien = (int)$row['don_gia_dien'];
        $don_gia_nuoc = (int)$row['don_gia_nuoc'];

        // Thành tiền
        $thanh_tien = ($so_dien * $don_gia_dien) + ($so_nuoc * $don_gia_nuoc);

        // Tạo hóa đơn
        return new HoaDon([
            'phong_id'      => $row['phong_id'],
            'so_dien_cu'    => $row['so_dien_cu'],
            'so_dien_moi'   => $row['so_dien_moi'],
            'so_nuoc_cu'    => $row['so_nuoc_cu'],
            'so_nuoc_moi'   => $row['so_nuoc_moi'],
            'don_gia_dien'  => $don_gia_dien,
            'don_gia_nuoc'  => $don_gia_nuoc,
            'thanh_tien'    => $thanh_tien,
            'thang'         => $row['thang'],
        ]);
    }
}
