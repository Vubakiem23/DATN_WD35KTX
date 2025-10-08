<?php

namespace Database\Seeders;

use App\Models\BaoCao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaoCaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         BaoCao::factory(10)->create();
    }
}
