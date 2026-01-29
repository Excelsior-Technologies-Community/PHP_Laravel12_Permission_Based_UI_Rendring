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

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Products
Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class);
});


// Role User 
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user.roles');
    Route::post('/user-roles/{user}', [UserRoleController::class, 'update'])->name('user.roles.update');
});
require __DIR__.'/auth.php';
