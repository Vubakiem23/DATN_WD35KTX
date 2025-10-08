<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['ma_quyen' => 'admin', 'ten_quyen' => 'admin',],
            ['ma_quyen' => 'manager', 'ten_quyen' => 'manager',],
            ['ma_quyen' => 'student', 'ten_quyen' => 'student',],
        ];
        DB::table('roles')->insert($roles);
    }
}
