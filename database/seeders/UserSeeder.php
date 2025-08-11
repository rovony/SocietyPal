<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Role;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $adminRole = Role::where('name', 'Admin_' . $society->id)->first();

        if ($society->id == 1) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'admin@example.com',
                'role_id' => $adminRole->id,
                'password' => bcrypt(123456),
                'society_id' => $society->id
            ]);
            $user->assignRole($adminRole);
        }
        else
        {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'role_id' => $adminRole->id,
                'password' => bcrypt(123456),
                'society_id' => $society->id
            ]);
            $user->assignRole($adminRole);
        }
    }
}
