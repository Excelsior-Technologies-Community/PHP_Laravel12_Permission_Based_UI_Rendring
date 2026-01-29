<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'], // check by email
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Assign role only if not already assigned
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
