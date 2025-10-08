<?php

namespace Database\Seeders;

use App\Models\TaiSan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaiSanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         TaiSan::factory(10)->create();
    }
}
