<?php

namespace App\Observers;

use App\Models\Khu;
use App\Models\ThongBaoKhuPhong;

class KhuObserver
{
    public function created(Khu $khu)
    {
        ThongBaoKhuPhong::create([
            'tieu_de' => 'Khu mới được thêm: ' . $khu->ten_khu,
            'noi_dung' => 'Khu "' . $khu->ten_khu . '" đã được thêm vào hệ thống.',
            'loai' => 'Khu',
            'doi_tuong_id' => $khu->id,
        ]);
    }
}
