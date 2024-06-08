<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrderController;
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

Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => 'akses tidak ditemukan'
    ], 401);
})->name('login');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::put('user-update/{id}', [AuthController::class, 'updateUser']);
Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);

Route::get('category', [CategoriesController::class, 'index']);
Route::post('category-tambah', [CategoriesController::class, 'store']);
Route::put('category-edit/{id}', [CategoriesController::class, 'update']);
Route::delete('category-hapus/{id}', [CategoriesController::class, 'destroy']);

Route::get('produk', [ProductController::class, 'index']);
Route::post('produk-tambah', [ProductController::class, 'store']);
Route::put('produk-edit/{id}', [ProductController::class, 'update']);
Route::delete('produk-hapus/{id}', [ProductController::class, 'destroy']);

Route::get('order', [OrderController::class, 'index'])->middleware('auth:sanctum');
Route::post('order-tambah', [OrderController::class, 'store'])->middleware('auth:sanctum');
Route::put('order-edit/{id}', [OrderController::class, 'update'])->middleware('auth:sanctum');

Route::post('add-toCart', [CartController::class, 'addToCart'])->middleware('auth:sanctum');
Route::put('editCart/{id}', [CartController::class, 'editCart'])->middleware('auth:sanctum');
Route::get('list-cart', [CartController::class, 'index'])->middleware('auth:sanctum');
Route::post('checkout', [CartController::class, 'checkout'])->middleware('auth:sanctum');
