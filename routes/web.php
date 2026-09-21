<?php

use App\Http\Controllers\AccessActivityController;
use App\Http\Controllers\PermissionManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleSimulatorController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Live Role Simulator & UI Policy Controls
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/simulator/switch', [RoleSimulatorController::class, 'switchRole'])->name('simulator.switch');
    Route::post('/simulator/exit', [RoleSimulatorController::class, 'exitSimulation'])->name('simulator.exit');
    Route::post('/simulator/policy', [RoleSimulatorController::class, 'togglePolicy'])->name('simulator.policy');
});


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
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
     * Product listing.
     */
    Route::get(
        '/products',
        [ProductController::class, 'index']
    )
        ->middleware('permission:view products')
        ->name('products.index');


    /*
     * Product statistics.
     */
    Route::get(
        '/products/statistics',
        [ProductController::class, 'statistics']
    )
        ->middleware('permission:view products')
        ->name('products.statistics');


    /*
     * Product CSV export.
     */
    Route::get(
        '/products/export',
        [ProductController::class, 'export']
    )
        ->middleware('permission:view products')
        ->name('products.export');


    /*
     * Create product form.
     */
    Route::get(
        '/products/create',
        [ProductController::class, 'create']
    )
        ->middleware('permission:create products')
        ->name('products.create');


    /*
     * Store product.
     */
    Route::post(
        '/products',
        [ProductController::class, 'store']
    )
        ->middleware('permission:create products')
        ->name('products.store');


    /*
     * Bulk delete.
     */
    Route::delete(
        '/products/bulk-delete',
        [ProductController::class, 'bulkDelete']
    )
        ->middleware('permission:delete products')
        ->name('products.bulk-delete');


    /*
     * Edit product.
     */
    Route::get(
        '/products/{product}/edit',
        [ProductController::class, 'edit']
    )
        ->middleware('permission:edit products')
        ->name('products.edit');


    /*
     * Update product.
     */
    Route::put(
        '/products/{product}',
        [ProductController::class, 'update']
    )
        ->middleware('permission:edit products')
        ->name('products.update');


    /*
     * Delete product.
     */
    Route::delete(
        '/products/{product}',
        [ProductController::class, 'destroy']
    )
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
    'role:super-admin',
])->group(function () {

    Route::get(
        '/user-roles',
        [UserRoleController::class, 'index']
    )->name('user.roles');

    Route::post(
        '/user-roles/{user}',
        [UserRoleController::class, 'update']
    )->name('user.roles.update');

});


/*
|--------------------------------------------------------------------------
| Super Admin - Permission Management
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-admin',
])->group(function () {

    Route::get(
        '/permissions',
        [PermissionManagementController::class, 'index']
    )->name('permissions.index');

    Route::post(
        '/permissions',
        [PermissionManagementController::class, 'store']
    )->name('permissions.store');

    Route::put(
        '/permissions/roles/{role}',
        [PermissionManagementController::class, 'updateRolePermissions']
    )->name('permissions.roles.update');

    Route::delete(
        '/permissions/{permission}',
        [PermissionManagementController::class, 'destroyPermission']
    )->name('permissions.destroy');

});



/*
|--------------------------------------------------------------------------
| Super Admin - Access Activity
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:super-admin',
])->group(function () {

    Route::get(
        '/access-activities',
        [AccessActivityController::class, 'index']
    )->name('access.activities');

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';