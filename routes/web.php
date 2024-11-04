<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ApiProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;
use Tymon\JWTAuth\Facades\JWTAuth;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');
// Route::middleware('auth:api')->get('me', [AuthController::class, 'me']);

//route resource for products

// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rute produk yang dilindungi middleware checkRole
  // Route::middleware(['auth:api', 'checkRole'])->group(function () {
    Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    // Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    // Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products-show/{id}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products-data', [\App\Http\Controllers\ProductController::class, 'getProductsData'])->name('products.data');

    Route::get('/profile-user/{id}', [\App\Http\Controllers\UserController::class, 'index'])->name('profile-user.index');
  // });

//   Route::middleware('auth', 'checkRole')->group(function () {
//     Route::get('products', [ApiProductController::class, 'index'])->name('products.index');
//     Route::post('products', [ApiProductController::class, 'store'])->name('products.store');
//     Route::get('products/{id}', [ApiProductController::class, 'show'])->name('products.show');
//     Route::put('products/{id}', [ApiProductController::class, 'update'])->name('products.update');
//     Route::delete('products/{id}', [ApiProductController::class, 'destroy'])->name('products.destroy');
// });
