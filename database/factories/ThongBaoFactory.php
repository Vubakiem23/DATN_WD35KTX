<?php

namespace Database\Factories;

use App\Models\ThongBao;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThongBaoFactory extends Factory
{
    protected $model = ThongBao::class;

    public function definition()
    {
        return [
            'tieu_de' => $this->faker->sentence,
            'noi_dung' => $this->faker->paragraph,
            'ngay_dang' => $this->faker->date,
            'doi_tuong' => $this->faker->randomElement(['Tất cả', 'Theo khu', 'Theo phòng']),
        ];
    }
}
