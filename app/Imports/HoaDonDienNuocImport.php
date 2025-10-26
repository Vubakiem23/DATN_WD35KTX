<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\HoaDon;

class HoaDonDienNuocImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $so_dien = (int)$row['so_dien_moi'] - (int)$row['so_dien_cu'];
        $so_nuoc = (int)$row['so_nuoc_moi'] - (int)$row['so_nuoc_cu'];

        $don_gia_dien = (int)$row['don_gia_dien'];
        $don_gia_nuoc = (int)$row['don_gia_nuoc'];

        $thanh_tien = ($so_dien * $don_gia_dien) + ($so_nuoc * $don_gia_nuoc);

        return new HoaDon([
            'phong_id'      => $row['phong_id'], // gán theo id phòng trong file Excel
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
