<?php

namespace Database\Seeders;

use App\Models\DangKyKTX;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DangKyKTXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DangKyKTX::factory(10)->create();
    }
}
