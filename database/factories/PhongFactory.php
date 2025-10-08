<?php

namespace Database\Factories;

use App\Models\Phong;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhongFactory extends Factory
{
    protected $model = Phong::class;

    public function definition()
    {
        return [
            'ten_phong' => $this->faker->word(),
            'khu' => $this->faker->randomElement(['A', 'B', 'C']),
            'loai_phong' => $this->faker->randomElement(['Đơn', 'Đôi']),
            'suc_chua' => $this->faker->numberBetween(1, 4),
            'trang_thai' => $this->faker->randomElement(['Trống', 'Đã ở', 'Bảo trì']),
        ];
    }
}

