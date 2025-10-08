<?php

namespace Database\Seeders;

use App\Models\SuCo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuCoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         SuCo::factory(10)->create();
    }
}
