<?php

namespace Database\Seeders;

use App\Models\ChuyenPhong;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChuyenPhongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         ChuyenPhong::factory(10)->create();
    }
}
