<?php

namespace Database\Factories;

use App\Models\DangKyKTX;
use Illuminate\Database\Eloquent\Factories\Factory;

class DangKyKTXFactory extends Factory
{
    protected $model = DangKyKTX::class;

    public function definition()
    {
        return [
            'sinh_vien_id' => $this->faker->numberBetween(1, 10),
            'ngay_dang_ky' => $this->faker->date,
            'trang_thai' => $this->faker->randomElement(['Chờ duyệt', 'Đã duyệt', 'Từ chối']),
        ];
    }
}
