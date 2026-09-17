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
    public function index(Request $request): View
    {
        $permissionSearch = trim(
            $request->input('permission_search', '')
        );

        $permissionGuard = $request->input(
            'permission_guard',
            ''
        );


        
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        | Permission search + guard filter
        |--------------------------------------------------------------------------
        */
        $permissions = Permission::query()
            ->when(
                $permissionSearch,
                function ($query) use ($permissionSearch) {
                    $query->where(
                        'name',
                        'like',
                        "%{$permissionSearch}%"
                    );
                }
            )
            ->when(
                $permissionGuard,
                function ($query) use ($permissionGuard) {
                    $query->where(
                        'guard_name',
                        $permissionGuard
                    );
                }
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Guards
        |--------------------------------------------------------------------------
        */
        $guards = Permission::query()
            ->select('guard_name')
            ->distinct()
            ->orderBy('guard_name')
            ->pluck('guard_name');

        return view(
            'permissions.index',
            compact(
                'roles',
                'permissions',
                'guards',
                'permissionSearch',
                'permissionGuard'
            )
        );
    }

    /**
     * Create a new permission.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
        ]);

        $permission = Permission::create([
            'name' => strtolower(
                trim($validated['name'])
            ),

            'guard_name' => 'web',
        ]);

        AccessActivity::create([
            'actor_id' => auth()->id(),

            'action' => 'permission_created',

            'target_type' => 'permission',

            'target_id' => $permission->id,

            'description' => auth()->user()->name
                . ' created permission "'
                . $permission->name
                . '"',

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

        $permissionIds =
            $validated['permissions'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | Existing permissions
        |--------------------------------------------------------------------------
        */
        $oldPermissions = $role->permissions
            ->pluck('name')
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | New permissions
        |--------------------------------------------------------------------------
        */
        $permissions = Permission::query()
            ->whereIn(
                'id',
                $permissionIds
            )
            ->where(
                'guard_name',
                'web'
            )
            ->get();

        $newPermissions = $permissions
            ->pluck('name')
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Sync permissions
        |--------------------------------------------------------------------------
        */
        $role->syncPermissions(
            $permissions
        );

        /*
        |--------------------------------------------------------------------------
        | Detect added permissions
        |--------------------------------------------------------------------------
        */
        $added = array_values(
            array_diff(
                $newPermissions,
                $oldPermissions
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Detect removed permissions
        |--------------------------------------------------------------------------
        */
        $removed = array_values(
            array_diff(
                $oldPermissions,
                $newPermissions
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Log added permissions
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Log removed permissions
        |--------------------------------------------------------------------------
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

        DB::transaction(
            function () use ($permission) {
                $permission->delete();
            }
        );

        AccessActivity::create([
            'actor_id' => auth()->id(),

            'action' => 'permission_deleted',

            'target_type' => 'permission',

            'target_id' => $permissionId,

            'description' => auth()->user()->name
                . ' deleted permission "'
                . $permissionName
                . '"',

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