<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

//route resource for products

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::get('/dashboard', [LoginController::class, 'main']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    //admin
    Route::get('/products', [ProductController::class, 'index']);
    Route::resource('/products', \App\Http\Controllers\ProductController::class);

    //user
    Route::get('/user', [UserController::class, 'index']);
    Route::resource('/user', \App\Http\Controllers\UserController::class);
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