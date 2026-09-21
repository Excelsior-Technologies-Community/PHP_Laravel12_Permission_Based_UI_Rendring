<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user has (or is simulating) the given role.
     */
    public function hasRole($roles, string $guard = null): bool
    {
        if (session()->has('simulated_role')) {
            $simRole = session('simulated_role');
            if (is_string($roles)) {
                return $simRole === $roles;
            }
            if (is_array($roles) || $roles instanceof \Illuminate\Support\Collection) {
                return in_array($simRole, (array) $roles, true);
            }
            if ($roles instanceof \Spatie\Permission\Contracts\Role) {
                return $simRole === $roles->name;
            }
            return false;
        }

        return $this->traitHasRole($roles, $guard);
    }

    /**
     * Check if user is currently simulating a role.
     */
    public function isSimulatingRole(): bool
    {
        return session()->has('simulated_role');
    }

    /**
     * Get active effective role name (real or simulated).
     */
    public function getActiveRoleName(): string
    {
        if (session()->has('simulated_role')) {
            return session('simulated_role');
        }

        return $this->roles->first()?->name ?? 'No Role';
    }

    /**
     * Original Spatie trait method alias.
     */
    protected function traitHasRole($roles, string $guard = null): bool
    {
        if (is_string($roles) && str_contains($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $guard
                ? $this->roles->where('guard_name', $guard)->contains('name', $roles)
                : $this->roles->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $guard
                ? $this->roles->where('guard_name', $guard)->contains('id', $roles)
                : $this->roles->contains('id', $roles);
        }

        if ($roles instanceof \Spatie\Permission\Contracts\Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->traitHasRole($role, $guard)) {
                    return true;
                }
            }
            return false;
        }

        return $roles->intersect($guard ? $this->roles->where('guard_name', $guard) : $this->roles)->isNotEmpty();
    }
}
