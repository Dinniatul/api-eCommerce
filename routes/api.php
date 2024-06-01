<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);

Route::get('category', [CategoriesController::class, 'index']);
Route::post('category-tambah', [CategoriesController::class, 'store']);
Route::put('category-edit/{id}', [CategoriesController::class, 'update']);
Route::delete('category-edit/{id}', [CategoriesController::class, 'destroy']);

Route::get('produk', [ProductController::class, 'index']);
Route::post('produk-tambah', [ProductController::class, 'store']);
Route::put('produk-edit/{id}', [ProductController::class, 'update']);
Route::delete('produk-hapus/{id}', [ProductController::class, 'destroy']);
