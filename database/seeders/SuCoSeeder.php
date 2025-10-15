<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuCo;
use App\Models\SinhVien;
use App\Models\Phong;
use Illuminate\Support\Str;

class SuCoSeeder extends Seeder
{
    public function run(): void
    {
        $sv = SinhVien::first();
        $ph = Phong::first();

        for ($i = 1; $i <= 10; $i++) {
            SuCo::create([
                'sinh_vien_id' => $sv->id ?? 1,
                'phong_id' => $ph->id ?? 1,
                'mo_ta' => 'Sự cố số ' . $i . ': ' . Str::random(30),
                'ngay_gui' => now()->subDays(rand(1, 7)),
                'trang_thai' => 'Chờ xử lý',
            ]);
        }
    }
}
