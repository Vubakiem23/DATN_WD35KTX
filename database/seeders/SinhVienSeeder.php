<?php

namespace Database\Seeders;

use Database\Factories\SinhVienFactory as FactoriesSinhVienFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SinhVien;
use SinhVienFactory;

class SinhVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         SinhVien::factory(10)->create(); 
    }
}
