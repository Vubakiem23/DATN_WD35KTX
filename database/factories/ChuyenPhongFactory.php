<?php

namespace Database\Factories;

use App\Models\ChuyenPhong;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChuyenPhongFactory extends Factory
{
    protected $model = ChuyenPhong::class;

    public function definition()
    {
        return [
            'sinh_vien_id' => $this->faker->numberBetween(1, 10),
            'phong_cu_id' => $this->faker->numberBetween(1, 10),
            'phong_moi_id' => $this->faker->numberBetween(1, 10),
            'ly_do' => $this->faker->sentence,
            'trang_thai' => $this->faker->randomElement(['Đang xử lý', 'Đã duyệt', 'Từ chối']),
        ];
    }
}
