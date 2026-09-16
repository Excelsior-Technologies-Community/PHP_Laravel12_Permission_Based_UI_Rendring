<?php

namespace App\Http\Controllers;

use App\Models\AccessActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManagementController extends Controller
{
    /**
     * Display roles and permissions.
     */
    public function index(): View
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('name')->get();

        return view('permissions.index', compact(
            'roles',
            'permissions'
        ));
    }

    /**
     * Create a new permission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
        ]);

        $permission = Permission::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        AccessActivity::create([
            'actor_id' => auth()->id(),
            'action' => 'permission_created',
            'target_type' => 'permission',
            'target_id' => $permission->id,
            'description' => auth()->user()->name
                . ' created permission "' . $permission->name . '"',
            'metadata' => [
                'permission' => $permission->name,
            ],
        ]);

        return back()->with(
            'success',
            'Permission created successfully.'
        );
    }

    /**
     * Update permissions assigned to a role.
     */
    public function updateRolePermissions(
        Request $request,
        Role $role
    ): RedirectResponse {
        /*
         * Validate submitted permission IDs.
         */
        $validated = $request->validate([
            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        /*
         * Get selected permission IDs.
         *
         * Example:
         * [1, 3, 6]
         */
        $permissionIds = $validated['permissions'] ?? [];

        /*
         * Get the role's current permissions
         * before updating them.
         */
        $oldPermissions = $role->permissions
            ->pluck('name')
            ->values()
            ->toArray();

        /*
         * IMPORTANT:
         *
         * Convert permission IDs into actual
         * Permission models.
         *
         * Do NOT pass numeric IDs directly to
         * syncPermissions().
         */
        $permissions = Permission::query()
            ->whereIn('id', $permissionIds)
            ->where('guard_name', 'web')
            ->get();

        /*
         * Get permission names after conversion.
         *
         * Example:
         *
         * [1, 3, 6]
         *
         * becomes:
         *
         * [
         *     'view products',
         *     'edit products',
         *     'delete products'
         * ]
         */
        $newPermissions = $permissions
            ->pluck('name')
            ->values()
            ->toArray();

        /*
         * Sync actual Permission models.
         */
        $role->syncPermissions($permissions);

        /*
         * Determine which permissions were added.
         */
        $added = array_values(array_diff(
            $newPermissions,
            $oldPermissions
        ));

        /*
         * Determine which permissions were removed.
         */
        $removed = array_values(array_diff(
            $oldPermissions,
            $newPermissions
        ));

        /*
         * Log added permissions.
         */
        if (!empty($added)) {

            AccessActivity::create([
                'actor_id' => auth()->id(),
                'action' => 'permissions_added',
                'target_type' => 'role',
                'target_id' => $role->id,

                'description' => auth()->user()->name
                    . ' added permissions to role "'
                    . $role->name
                    . '"',

                'metadata' => [
                    'role' => $role->name,
                    'permissions' => $added,
                ],
            ]);
        }

        /*
         * Log removed permissions.
         */
        if (!empty($removed)) {

            AccessActivity::create([
                'actor_id' => auth()->id(),
                'action' => 'permissions_removed',
                'target_type' => 'role',
                'target_id' => $role->id,

                'description' => auth()->user()->name
                    . ' removed permissions from role "'
                    . $role->name
                    . '"',

                'metadata' => [
                    'role' => $role->name,
                    'permissions' => $removed,
                ],
            ]);
        }

        /*
         * Return to permission management page.
         */
        return back()->with(
            'success',
            'Permissions updated successfully for "'
            . $role->name
            . '".'
        );
    }

    /**
     * Delete a permission.
     */
    public function destroyPermission(
        Permission $permission
    ): RedirectResponse {
        $permissionName = $permission->name;
        $permissionId = $permission->id;

        DB::transaction(function () use ($permission) {
            $permission->delete();
        });

        AccessActivity::create([
            'actor_id' => auth()->id(),
            'action' => 'permission_deleted',
            'target_type' => 'permission',
            'target_id' => $permissionId,

            'description' => auth()->user()->name
                . ' deleted permission "' . $permissionName . '"',

            'metadata' => [
                'permission' => $permissionName,
            ],
        ]);

        return back()->with(
            'success',
            'Permission deleted successfully.'
        );
    }
}