<?php

use App\Http\Controllers\AccessActivityController;
use App\Http\Controllers\PermissionManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
|
| Backend permission protection is applied here.
|
*/

Route::middleware('auth')->group(function () {

    // View products
    Route::get('/products', [
        ProductController::class,
        'index'
    ])
        ->middleware('permission:view products')
        ->name('products.index');

    // Create product form
    Route::get('/products/create', [
        ProductController::class,
        'create'
    ])
        ->middleware('permission:create products')
        ->name('products.create');

    // Store product
    Route::post('/products', [
        ProductController::class,
        'store'
    ])
        ->middleware('permission:create products')
        ->name('products.store');

    // Edit product form
    Route::get('/products/{product}/edit', [
        ProductController::class,
        'edit'
    ])
        ->middleware('permission:edit products')
        ->name('products.edit');

    // Update product
    Route::put('/products/{product}', [
        ProductController::class,
        'update'
    ])
        ->middleware('permission:edit products')
        ->name('products.update');

    // Delete product
    Route::delete('/products/{product}', [
        ProductController::class,
        'destroy'
    ])
        ->middleware('permission:delete products')
        ->name('products.destroy');

});

/*
|--------------------------------------------------------------------------
| Super Admin - User Access Management
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-admin'
])->group(function () {

    Route::get('/user-roles', [
        UserRoleController::class,
        'index'
    ])->name('user.roles');

    Route::post('/user-roles/{user}', [
        UserRoleController::class,
        'update'
    ])->name('user.roles.update');

});

/*
|--------------------------------------------------------------------------
| Super Admin - Permission Management
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-admin'
])->group(function () {

    Route::get('/permissions', [
        PermissionManagementController::class,
        'index'
    ])->name('permissions.index');

    Route::post('/permissions', [
        PermissionManagementController::class,
        'store'
    ])->name('permissions.store');

    Route::put('/permissions/roles/{role}', [
        PermissionManagementController::class,
        'updateRolePermissions'
    ])->name('permissions.roles.update');

    Route::delete('/permissions/{permission}', [
        PermissionManagementController::class,
        'destroyPermission'
    ])->name('permissions.destroy');

});

/*
|--------------------------------------------------------------------------
| Super Admin - Access Activity
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-admin'
])->group(function () {

    Route::get('/access-activities', [
        AccessActivityController::class,
        'index'
    ])->name('access.activities');

});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';