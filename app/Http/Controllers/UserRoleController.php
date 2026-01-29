<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('users.roles', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->syncRoles([$request->role]);
        return back()->with('success', 'Role updated!');
    }
}

