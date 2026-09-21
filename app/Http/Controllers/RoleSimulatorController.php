<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleSimulatorController extends Controller
{
    /**
     * Switch the active simulated role in session.
     */
    public function switchRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|string',
        ]);

        $role = $validated['role'];

        if ($role === 'exit' || $role === 'none') {
            session()->forget('simulated_role');
            return back()->with('success', 'Exited UI simulation mode. Viewing as your original role.');
        }

        // Check if role exists or is normal_user / guest
        $validRoles = ['super-admin', 'admin', 'staff', 'viewer', 'normal-user', 'guest'];
        $dbRoles = Role::pluck('name')->toArray();
        $allowed = array_unique(array_merge($validRoles, $dbRoles));

        if (!in_array($role, $allowed, true)) {
            return back()->with('error', "Invalid simulation role '{$role}'.");
        }

        session(['simulated_role' => $role]);

        return back()->with('success', "Live UI Simulation Active: Viewing system exactly as '{$role}'!");
    }

    /**
     * Exit UI role simulation mode.
     */
    public function exitSimulation(): RedirectResponse
    {
        session()->forget('simulated_role');
        return back()->with('success', 'Exited preview mode. Restored full administrative UI.');
    }

    /**
     * Toggle UI Policy: Hide vs Disabled-Lock (🔒).
     */
    public function togglePolicy(Request $request): RedirectResponse
    {
        $policy = $request->input('policy', 'disabled_lock');
        if (!in_array($policy, ['disabled_lock', 'hide'], true)) {
            $policy = 'disabled_lock';
        }

        session(['ui_policy' => $policy]);

        $label = $policy === 'disabled_lock' ? 'Disabled with Lock Tooltip (🔒)' : 'Completely Hidden';
        return back()->with('success', "UI Policy Updated: Unauthorized features are now {$label}.");
    }
}
