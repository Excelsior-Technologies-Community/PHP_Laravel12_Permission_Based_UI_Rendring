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
            'export products',
            'view product reports',

            // Sensitive field-level permissions
            'view product costs',
            'edit product costs',
        ];

        /*
         * Create permissions.
         */
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
         * Create roles.
         */
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        /*
         * Admin permissions
         */
        $admin->syncPermissions([
            'view products',
            'create products',
            'edit products',
            'delete products',
            'export products',
            'view product reports',
            'view product costs',
            'edit product costs',
        ]);

        /*
         * Staff permissions (No delete, no sensitive costs access)
         */
        $staff->syncPermissions([
            'view products',
            'edit products',
        ]);

        /*
         * Viewer permissions (Read-only basic product info)
         */
        $viewer->syncPermissions([
            'view products',
        ]);

        /*
         * Super Admin gets all permissions.
         */
        $superAdmin->syncPermissions(Permission::all());
    }
}