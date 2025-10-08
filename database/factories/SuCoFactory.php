<?php

namespace Database\Factories;

use App\Models\SuCo;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuCoFactory extends Factory
{
    protected $model = SuCo::class;

    public function definition()
    {
        return [
            'sinh_vien_id' => $this->faker->numberBetween(1, 10),
            'phong_id' => $this->faker->numberBetween(1, 10),
            'mo_ta' => $this->faker->sentence,
            'ngay_gui' => $this->faker->date,
            'trang_thai' => $this->faker->randomElement(['Đang xử lý', 'Đã xử lý']),
        ];
    }
}
