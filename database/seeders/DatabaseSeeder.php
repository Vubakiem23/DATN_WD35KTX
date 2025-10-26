<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class,
            PhongSeeder::class,
            SinhVienSeeder::class,
            BaoCaoSeeder::class,
            ThongBaoSeeder::class,
            ChuyenPhongSeeder::class,
            DangKyKTXSeeder::class,
            HoaDonSeeder::class,
            QuanLySeeder::class,
            SuCoSeeder::class,
            TaiSanSeeder::class,
            ViolationTypeSeeder::class,
        ]);
    }
}
