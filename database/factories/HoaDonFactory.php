<?php

namespace Database\Factories;

use App\Models\HoaDon;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoaDonFactory extends Factory
{
    protected $model = HoaDon::class;

    public function definition()
    {
        return [
            'sinh_vien_id' => $this->faker->numberBetween(1, 10), // Giả sử có 10 sinh viên
            'loai_phi' => $this->faker->randomElement(['Tiền phòng', 'Điện', 'Nước']),
            'so_tien' => $this->faker->randomFloat(2, 100, 1000),
            'ngay_tao' => $this->faker->date,
            'trang_thai' => $this->faker->randomElement(['Đã thanh toán', 'Chưa thanh toán']),
        ];
    }
}
