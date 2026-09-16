<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Additional permission for demonstration
            'export products',
            'view product reports',
        ];

        /*
         * Create permissions.
         */
        foreach ($permissions as $permission) {

            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ]
            );

        }

        /*
         * Create roles.
         */
        $admin = Role::firstOrCreate(
            [
                'name' => 'admin',
                'guard_name' => 'web',
            ]
        );

        $staff = Role::firstOrCreate(
            [
                'name' => 'staff',
                'guard_name' => 'web',
            ]
        );

        $superAdmin = Role::firstOrCreate(
            [
                'name' => 'super-admin',
                'guard_name' => 'web',
            ]
        );

        /*
         * Admin
         */
        $admin->syncPermissions([
            'view products',
            'create products',
            'edit products',
            'delete products',
        ]);

        /*
         * Staff
         */
        $staff->syncPermissions([
            'view products',
            'edit products',
        ]);

        /*
         * Super Admin gets all permissions.
         *
         * Gate::before() also gives Super Admin
         * global bypass.
         */
        $superAdmin->syncPermissions(
            Permission::all()
        );
    }
}