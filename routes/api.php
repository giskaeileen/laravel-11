<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Api\ApiProductController;
// use App\Http\Controllers\ProductController;
// use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckToken;
use Tymon\JWTAuth\Facades\JWTAuth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/refresh-token-api', [\App\Http\Controllers\Api\AuthController::class, 'refreshToken'])->name('refresh.token.api');

// Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');
// Route::middleware('auth:api')->get('me', [AuthController::class, 'me']);

Route::middleware(['checkToken'])->group(function () {
  Route::get('/products-role-api', [\App\Http\Controllers\Api\ProductController::class, 'role'])->name('products.role.api');
  Route::get('/products-data-api', [\App\Http\Controllers\Api\ProductController::class, 'getProductsData'])->name('products.data.api');
  Route::get('/products-data-show-api/{id}', [\App\Http\Controllers\Api\ProductController::class, 'getData'])->name('products.datashow.api');
  Route::post('/products-store-api', [\App\Http\Controllers\Api\ProductController::class, 'store'])->name('products.store');
  Route::post('/products-update/{id}', [\App\Http\Controllers\Api\ProductController::class, 'update'])->name('products.update.api');
  Route::delete('/products-delete/{id}', [\App\Http\Controllers\Api\ProductController::class, 'destroy'])->name('products.destroy.api');
  Route::post('/logout-api', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout.api');

  Route::get('/user-api', [\App\Http\Controllers\Api\UserController::class, 'getUser'])->name('user.api');
  Route::get('/user-data-api', [\App\Http\Controllers\Api\UserController::class, 'getUserData'])->name('user.data.api');
  Route::post('/user-upload-photo-api', [\App\Http\Controllers\Api\UserController::class, 'uploadImage'])->name('user.uploadimage.api');
  Route::post('/user-update-api', [\App\Http\Controllers\Api\UserController::class, 'updateUserProfile'])->name('user.update.api');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();

});