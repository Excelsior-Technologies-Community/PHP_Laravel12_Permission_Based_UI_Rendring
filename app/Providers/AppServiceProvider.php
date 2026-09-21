<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * Gate before callback supporting Live Role Simulation.
         */
        Gate::before(function (User $user, string $ability) {
            if (session()->has('simulated_role')) {
                $simRole = session('simulated_role');

                if ($simRole === 'super-admin') {
                    return true;
                }

                if ($simRole === 'guest' || $simRole === 'normal-user') {
                    return false;
                }

                $role = Role::where('name', $simRole)->first();
                if ($role && $role->hasPermissionTo($ability)) {
                    return true;
                }

                return false;
            }

            return $user->hasRole('super-admin') ? true : null;
        });

        /*
         * Blade Directives for UI Policy checking.
         */
        Blade::if('uican', function (string $permission) {
            return auth()->check() && auth()->user()->can($permission);
        });

        Blade::if('uirole', function (string $role) {
            if (!auth()->check()) {
                return false;
            }
            if (session()->has('simulated_role')) {
                return session('simulated_role') === $role;
            }
            return auth()->user()->hasRole($role);
        });
    }
}
