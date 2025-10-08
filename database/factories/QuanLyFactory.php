<?php

namespace Database\Factories;

use App\Models\QuanLy;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuanLyFactory extends Factory
{
    protected $model = QuanLy::class;

    public function definition()
    {
        static $usedUserIds = [];

        $user = User::whereNotIn('id', $usedUserIds)->inRandomOrder()->first();

        if (!$user) {
            $user = User::factory()->create();

            $managerRole = Role::where('ten_quyen', 'manager')->first();

            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $managerRole->id,
            ]);
        }

        $usedUserIds[] = $user->id;

        return [
            'ma_quan_ly' => 'QL' . $this->faker->unique()->numerify('###'),
            'ho_ten' => $this->faker->name,
            'chuc_vu' => $this->faker->jobTitle,
            'user_id' => $user->id,
        ];
    }
}
