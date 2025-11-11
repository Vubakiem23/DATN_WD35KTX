<?php

namespace App\Observers;

use App\Models\Phong;
use App\Models\ThongBaoKhuPhong;

class PhongObserver
{
    public function created(Phong $phong)
    {
        ThongBaoKhuPhong::create([
            'tieu_de' => 'Phòng mới được thêm: ' . $phong->ten_phong,
            'noi_dung' => 'Phòng "' . $phong->ten_phong . '" thuộc khu ID ' . $phong->khu_id . ' đã được thêm vào hệ thống.',
            'loai' => 'Phòng',
            'doi_tuong_id' => $phong->id,
        ]);
    }
}


