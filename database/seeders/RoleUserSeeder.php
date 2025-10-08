<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('ten_quyen', 'admin')->first();
        $managerRole = Role::where('ten_quyen', 'manager')->first();
        $studentRole = Role::where('ten_quyen', 'student')->first();

        $admin = User::where('email', 'admin@gmail.com')->first();
        $manager = User::where('email', 'manager@gmail.com')->first();
        $student = User::where('email', 'student@gmail.com')->first();

        $user_roles = [
            [
                'user_id' => $admin->id,
                'role_id' => $adminRole->id
            ],
            [
                'user_id' => $manager->id,
                'role_id' => $managerRole->id
            ],
            [
                'user_id' => $student->id,
                'role_id' => $studentRole->id
            ],
        ];

        DB::table('role_users')->insert($user_roles);
    }
}
