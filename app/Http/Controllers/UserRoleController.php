<?php

namespace App\Http\Controllers;

use App\Models\AccessActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display users with role filtering and searching.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $roleFilter = $request->input('role');

        $users = User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query) use ($roleFilter) {
                $query->role($roleFilter);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('users.roles', compact(
            'users',
            'roles',
            'search',
            'roleFilter'
        ));
    }

    /**
     * Update a user's role.
     */
    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'role' => [
                'required',
                'exists:roles,name',
            ],
        ]);

        $newRole = $validated['role'];

        /*
         * Prevent Super Admin from removing their own
         * Super Admin role.
         */
        if (
            $user->id === auth()->id()
            && $user->hasRole('super-admin')
            && $newRole !== 'super-admin'
        ) {
            return back()->with(
                'error',
                'You cannot remove your own Super Admin role.'
            );
        }

        $oldRole = $user->getRoleNames()->first();

        if ($oldRole === $newRole) {
            return back()->with(
                'success',
                'User already has the "' . $newRole . '" role.'
            );
        }

        $user->syncRoles([$newRole]);

        AccessActivity::create([
            'actor_id' => auth()->id(),
            'action' => 'user_role_changed',
            'target_type' => 'user',
            'target_id' => $user->id,
            'description' => auth()->user()->name
                . ' changed ' . $user->name
                . '\'s role from "'
                . ($oldRole ?? 'none')
                . '" to "'
                . $newRole
                . '"',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ],
        ]);

        return back()->with(
            'success',
            'Role updated successfully for ' . $user->name . '.'
        );
    }
}