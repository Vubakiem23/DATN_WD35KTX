<?php

namespace Database\Seeders;

use App\Models\QuanLy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuanLySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         QuanLy::factory(10)->create();
    }
}
