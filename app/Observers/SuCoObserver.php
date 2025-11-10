<?php

namespace App\Observers;

use App\Models\SuCo;
use App\Models\ThongBaoSuCo;

class SuCoObserver
{
    /**
     * Handle the SuCo "created" event.
     */
    public function created(SuCo $suCo): void
    {
        // Khi có sự cố mới, tạo thông báo tương ứng
        ThongBaoSuCo::create([
            'su_co_id' => $suCo->id,
            'tieu_de' => 'Sự cố mới được báo cáo',
            'noi_dung' => 'Phòng ' . $suCo->phong_id . ' có sự cố: ' . $suCo->mo_ta,
            'ngay_tao' => now(),
        ]);
    }
}
