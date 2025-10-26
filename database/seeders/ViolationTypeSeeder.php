<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ViolationType;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        ViolationType::firstOrCreate(
            ['code' => 'LATE_RENT'],
            ['name' => 'Thanh toán chậm tiền nhà', 'description' => 'Quá hạn thanh toán ký túc xá']
        );
    }
}
