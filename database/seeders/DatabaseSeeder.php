<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Create roles and permissions first.
         */
        $this->call(RolePermissionSeeder::class);

        /*
         * Super Admin
         */
        $superAdmin = User::firstOrCreate(
            [
                'email' => 'superadmin@example.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        $superAdmin->syncRoles(['super-admin']);

        /*
         * Admin
         */
        $admin = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $admin->syncRoles(['admin']);

        /*
         * Staff
         */
        $staff = User::firstOrCreate(
            [
                'email' => 'staff@example.com',
            ],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );

        $staff->syncRoles(['staff']);

        /*
         * Normal User
         */
        $user = User::firstOrCreate(
            [
                'email' => 'user@example.com',
            ],
            [
                'name' => 'Normal User',
                'password' => bcrypt('password'),
            ]
        );

        $user->syncRoles([]);
    }
}