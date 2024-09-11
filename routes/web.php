<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return view('welcome');
});

//route resource for products

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::get('/dashboard', [LoginController::class, 'main']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute produk yang dilindungi middleware checkRole
Route::middleware(['auth', 'checkRole'])->group(function () {
    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
  });

// Route::middleware('auth')->group(function () {
    
//     // Route untuk pengguna dengan role 'user', hanya bisa melihat (GET index)
//     Route::middleware('role:user')->group(function () {
//         Route::get('/products', [ProductController::class, 'index'])->name('products.index');
//     });

//     // Route untuk admin dengan akses penuh (CRUD)
//     Route::middleware('role:admin')->group(function () {
//         Route::resource('/products', ProductController::class)->except(['index']); // Semua kecuali index
//     });
// });