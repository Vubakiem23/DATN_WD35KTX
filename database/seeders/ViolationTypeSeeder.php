<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ViolationType;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['code' => 'LATE_RENT',   'name' => 'Thanh toán chậm tiền nhà',                               'description' => 'Quá hạn thanh toán ký túc xá'],
            ['code' => 'DISTURBANCE', 'name' => 'Gây rối KTX',                                             'description' => 'Gây ồn ào, xô xát, làm mất trật tự KTX'],
            ['code' => 'VANDALISM',   'name' => 'Phá hoại tài sản',                                        'description' => 'Làm hư hỏng, cố ý phá hoại tài sản/trang thiết bị'],
            ['code' => 'SAFETY_FIRE', 'name' => 'Vi phạm về an toàn – phòng cháy chữa cháy (PCCC)',        'description' => 'Vi phạm quy định an toàn, PCCC'],
            ['code' => 'OTHER',       'name' => 'Khác (ghi vào ghi chú)',                                  'description' => 'Các vi phạm khác không thuộc nhóm trên'],
        ];

        foreach ($items as $it) {
            ViolationType::updateOrCreate(['code' => $it['code']], $it);
        }
    }
}
