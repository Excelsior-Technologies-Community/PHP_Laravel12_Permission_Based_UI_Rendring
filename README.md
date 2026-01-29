# PHP_Laravel12_Permission_Based_UI_Rendring

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel Version"></a>
<a href="#"><img src="https://img.shields.io/badge/RBAC-Enabled-blue" alt="RBAC System"></a>
<a href="#"><img src="https://img.shields.io/badge/Auth-Breeze-green" alt="Auth System"></a>
<a href="#"><img src="https://img.shields.io/badge/Permissions-Spatie-orange" alt="Spatie Permission"></a>
<a href="#"><img src="https://img.shields.io/badge/PHP-8.2+-purple" alt="PHP Version"></a>
<a href="#"><img src="https://img.shields.io/badge/License-MIT-lightgrey" alt="License"></a>
</p>


##  Overview

This project demonstrates how to implement **Role-Based Access Control (RBAC)** in **Laravel 12** using the **Spatie Laravel Permission** package.
It provides a complete example of how to manage **user roles, permissions, and access control** for both the user interface and backend routes.

The system ensures that users only see and perform actions they are authorized for, while a **Super Admin** role has full access across the application.

---

##  Features

* Laravel 12 authentication using **Laravel Breeze**
* Role & Permission management using **Spatie Permission**
* Permission-based UI rendering in Blade templates
* Route protection using middleware
* Super Admin bypass using Gate
* Product management module with controlled access
* User Role Management panel for assigning roles
* Clean separation of roles: Super Admin, Admin, Staff, and Users

---

##  Folder Structure

```
app/
 ├── Models/
 │    ├── User.php
 │    └── Product.php
 │
 ├── Http/Controllers/
 │    ├── ProductController.php
 │    └── UserRoleController.php
 │
 └── Providers/
      └── AppServiceProvider.php

bootstrap/
 └── app.php   (Middleware registration)

database/
 ├── migrations/
 │    └── create_products_table.php
 └── seeders/
      └── RolePermissionSeeder.php

resources/views/
 ├── products/
 │    ├── index.blade.php
 │    ├── create.blade.php
 │    └── edit.blade.php
 │
 └── users/
      └── roles.blade.php

routes/
 └── web.php
```


---

## STEP 1 — Install Laravel 12

```bash
composer create-project laravel/laravel rbac-app

php artisan serve
```

---

## STEP 2 — Configure Database

**Edit `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=permission
DB_USERNAME=root
DB_PASSWORD=
```

**Run:**

```bash
php artisan migrate
```

---

## STEP 3 — Install Authentication (Laravel Breeze)

```bash
composer require laravel/breeze --dev

php artisan breeze:install

npm install && npm run build

php artisan migrate
```

---

## STEP 4 — Install Spatie Permission Package

```bash
composer require spatie/laravel-permission

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

php artisan migrate
```

---

## STEP 5 — Update User Model

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## STEP 6 — Register Spatie Middleware (IMPORTANT for Laravel 12)

**File:** `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## STEP 7 — Create Roles & Permissions Seeder

```bash
php artisan make:seeder RolePermissionSeeder
```

**File:** `database/seeders/RolePermissionSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // 👑 Super Admin Role
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);

        $admin->syncPermissions(Permission::all());

        $staff->syncPermissions([
            'view products',
            'edit products',
        ]);
    }
}
```

**Run seeder:**

```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

## STEP 8 — Make First User Super Admin

```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();
$user->assignRole('super-admin');
```

---

## STEP 9 — Super Admin Bypass Gate

**File:** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
```

---

## STEP 10 — Create Product Module

```bash
php artisan make:model Product –m
```

**Migration:** `database/migrations/create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

```bash
php artisan migrate
```

**Model:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];
}
```

---

## STEP 11 — Product Controller

```bash
php artisan make:controller ProductController –resource
```

**File:** `app/Http/Controllers/ProductController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        Product::create($request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]));

        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]));

        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }
}
```

---

## STEP 12 — Routes

**File:** `routes/web.php`

```php
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserRoleController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user.roles');
    Route::post('/user-roles/{user}', [UserRoleController::class, 'update'])->name('user.roles.update');
});

require __DIR__.'/auth.php';
```

---

## STEP 13 — Product Views (Blade Templates)

**Folder:** `resources/views/products/`

### index.blade.php

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Products</h2>
    </x-slot>

    <div class="p-6">

        @can('create products')
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                Add Product
            </a>
        @endcan

        <table class="mt-4 w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Price</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="p-2 border">{{ $product->id }}</td>
                        <td class="p-2 border">{{ $product->name }}</td>
                        <td class="p-2 border">₹{{ $product->price }}</td>
                        <td class="p-2 border flex gap-2">

                            @can('edit products')
                                <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 px-2 py-1 rounded text-white">Edit</a>
                            @endcan

                            @can('delete products')
                                <form action="{{ route('products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 px-2 py-1 rounded text-white">Delete</button>
                                </form>
                            @endcan

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
```

### create.blade.php

```blade
<x-app-layout>
<div class="p-6">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <input name="name" placeholder="Name" class="border p-2"><br><br>
        <input name="price" placeholder="Price" class="border p-2"><br><br>
        <button class="bg-green-600 text-white px-4 py-2">Save</button>
    </form>
</div>
</x-app-layout>
```

### edit.blade.php

```blade
<x-app-layout>
<div class="p-6">
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')
        <input name="name" value="{{ $product->name }}" class="border p-2"><br><br>
        <input name="price" value="{{ $product->price }}" class="border p-2"><br><br>
        <button class="bg-blue-600 text-white px-4 py-2">Update</button>
    </form>
</div>
</x-app-layout>
```

---

## STEP 14 — Role Based Navigation Menu

**File:** `resources/views/layouts/navigation.blade.php`

```blade
@hasanyrole('admin|staff|super-admin') 
<x-nav-link :href="route('products.index')">Products</x-nav-link> 
@endhasanyrole 
 
@role('super-admin') 
<x-nav-link :href="route('user.roles')">User Roles</x-nav-link> 
@endrole
```

---

## STEP 15 — User Role Management Panel

```bash
php artisan make:controller UserRoleController
```

**Controller:** `app/Http/Controllers/UserRoleController.php`

```php
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
```

**Blade:** `resources/views/users/roles.blade.php`

```blade
<x-app-layout>
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Manage User Roles</h2>

    @if(session('success'))
        <div class="bg-green-200 p-2 mb-3">{{ session('success') }}</div>
    @endif

    <table class="w-full border">
        <tr class="bg-gray-100">
            <th class="p-2 border">Name</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Role</th>
            <th class="p-2 border">Action</th>
        </tr>

        @foreach($users as $user)
            <tr>
                <td class="p-2 border">{{ $user->name }}</td>
                <td class="p-2 border">{{ $user->email }}</td>
                <td class="p-2 border">{{ $user->getRoleNames()->first() }}</td>
                <td class="p-2 border">
                    <form method="POST" action="{{ route('user.roles.update', $user) }}">
                        @csrf
                        <select name="role" class="border p-1">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <button class="bg-blue-600 text-white px-2 py-1 ml-2">Update</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
</x-app-layout>
```

---

## FINAL COMMANDS

```bash
php artisan optimize:clear

php artisan permission:cache-reset
```

---

## FINAL PROJECT OUTPUT (WITH TINKER SUPER ADMIN USER)

```bash
php artisan tinker
```

```php
use App\Models\User;

$user = User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);

$user->assignRole('super-admin');
```

### 1. Login System

Users can:

* Register
* Login
* Logout
  After login, access depends on their role.

### 2. Roles in the System

| Role        | Access Level                                |
| ----------- | ------------------------------------------- |
| Super-admin | Full system access (bypass all permissions) |
| admin       | Manage products                             |
| staff       | View + edit products only                   |
| normal user | No product management access                |

### 3. Products Module Output

#### Add Product Button

| Role        | Can See "Add Product"? |
| ----------- | ---------------------- |
| Super-admin | ✅ Yes                  |
| admin       | ✅ Yes                  |
| staff       | ❌ No                   |
| user        | ❌ No                   |

super-admin:-

<img width="1919" height="566" alt="Screenshot 2026-01-28 173906" src="https://github.com/user-attachments/assets/82736bbf-b5cf-4dad-80e0-d0b3944269da" />

<img width="1919" height="406" alt="Screenshot 2026-01-28 174207" src="https://github.com/user-attachments/assets/866cdc72-c1f2-4fae-9eb9-f55b52bfad29" />


Admin:-

<img width="1919" height="305" alt="Screenshot 2026-01-28 174454" src="https://github.com/user-attachments/assets/4e5130c8-ad29-46c3-a944-37fbf03c5392" />

<img width="1919" height="406" alt="Screenshot 2026-01-28 174207" src="https://github.com/user-attachments/assets/002f7249-3180-4bee-a863-b5815b8b7cad" />


Staff:-

<img width="1919" height="311" alt="Screenshot 2026-01-28 174502" src="https://github.com/user-attachments/assets/e94e8578-9bd7-44b3-a37f-49e4a2970606" />

<img width="1919" height="378" alt="Screenshot 2026-01-28 174303" src="https://github.com/user-attachments/assets/0aa4076f-a6e5-4474-b888-02da69f52117" />



#### Edit Button

| Role        | Can Edit? |
| ----------- | --------- |
| Super-admin | ✅         |
| admin       | ✅         |
| staff       | ✅         |
| user        | ❌         |

super-admin:-

<img width="318" height="246" alt="Screenshot 2026-01-29 112427" src="https://github.com/user-attachments/assets/99b2ffdf-4b19-4c81-a913-77d4f8aa694a" />

Admin:-

<img width="318" height="246" alt="Screenshot 2026-01-29 112427" src="https://github.com/user-attachments/assets/5b905435-eb0c-4484-aa97-a77ec9bd06bc" />

Staff:-

<img width="318" height="246" alt="Screenshot 2026-01-29 112427" src="https://github.com/user-attachments/assets/cdfe0003-5d19-49d9-beca-8cb8d03817e1" />


#### Delete Button

| Role        | Can Delete? |
| ----------- | ----------- |
| Super-admin | ✅           |
| admin       | ✅           |
| staff       | ❌           |
| user        | ❌           |

super-admin:-

<img width="1919" height="193" alt="Screenshot 2026-01-29 112936" src="https://github.com/user-attachments/assets/6a4c658d-3ce4-47fd-9768-176fa0f1edfa" />

Admin:-

<img width="1919" height="193" alt="Screenshot 2026-01-29 112936" src="https://github.com/user-attachments/assets/7e43d967-2d84-4b5f-a8e3-48467ddda294" />

Staff:-

<img width="1919" height="378" alt="Screenshot 2026-01-28 174303" src="https://github.com/user-attachments/assets/ad7ce5f5-ea0e-47bb-b846-b185c5c1813c" />
