<?php

namespace Database\Factories;

use App\Models\TaiSan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaiSanFactory extends Factory
{
    protected $model = TaiSan::class;

    public function definition()
    {
        return [
            'ten_tai_san' => $this->faker->word,
            'phong_id' => $this->faker->numberBetween(1, 10),
            'so_luong' => $this->faker->numberBetween(1, 10),
            'tinh_trang' => $this->faker->randomElement(['Tốt', 'Hỏng', 'Bảo trì']),
        ];
    }
}
