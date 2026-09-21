<?php

namespace Database\Seeders;

use App\Models\Product;
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
            ['email' => 'superadmin@example.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('password')]
        );
        $superAdmin->syncRoles(['super-admin']);

        /*
         * Admin
         */
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $admin->syncRoles(['admin']);

        /*
         * Staff
         */
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            ['name' => 'Staff User', 'password' => bcrypt('password')]
        );
        $staff->syncRoles(['staff']);

        /*
         * Viewer (Read-only)
         */
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Viewer User', 'password' => bcrypt('password')]
        );
        $viewer->syncRoles(['viewer']);

        /*
         * Normal User
         */
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Normal User', 'password' => bcrypt('password')]
        );
        $user->syncRoles([]);

        /*
         * Sample Products with Sensitive Cost & Margin Data
         */
        $sampleProducts = [
            [
                'name' => 'Apple MacBook Pro 16" M3 Max',
                'price' => 3499.00,
                'cost_price' => 2450.00,
                'profit_margin' => 29.98,
                'supplier_code' => 'SUP-APPLE-GLOBAL',
            ],
            [
                'name' => 'Dell UltraSharp 32" 4K Thunderbolt Hub Monitor',
                'price' => 899.99,
                'cost_price' => 520.00,
                'profit_margin' => 42.22,
                'supplier_code' => 'SUP-DELL-DIST-99',
            ],
            [
                'name' => 'Logitech MX Master 3S Wireless Mouse',
                'price' => 99.99,
                'cost_price' => 45.00,
                'profit_margin' => 55.00,
                'supplier_code' => 'SUP-LOGI-ASIA',
            ],
            [
                'name' => 'Keychron Q1 Pro Custom Mechanical Keyboard',
                'price' => 199.00,
                'cost_price' => 95.00,
                'profit_margin' => 52.26,
                'supplier_code' => 'SUP-KEYCHRON-DIRECT',
            ],
            [
                'name' => 'Sony WH-1000XM5 Noise Canceling Headphones',
                'price' => 398.00,
                'cost_price' => 230.00,
                'profit_margin' => 42.21,
                'supplier_code' => 'SUP-SONY-AUDIO-HQ',
            ],
        ];

        foreach ($sampleProducts as $p) {
            Product::updateOrCreate(
                ['name' => $p['name']],
                $p
            );
        }
    }
}