<?php

namespace Database\Factories;

use App\Models\BaoCao;
use Illuminate\Database\Eloquent\Factories\Factory;

class BaoCaoFactory extends Factory
{
    protected $model = BaoCao::class;

    public function definition()
    {
        return [
            'loai_bao_cao' => $this->faker->randomElement(['Báo cáo tài chính', 'Báo cáo tình hình']),
            'noi_dung' => $this->faker->paragraph,
            'ngay_tao' => $this->faker->date,
        ];
    }
}
