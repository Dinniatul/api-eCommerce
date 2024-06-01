<?php

use App\Http\Controllers\AdminController\AuthController;
use App\Http\Controllers\AdminController\CategoriesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'login'])->name('/');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register_action']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_action']);


Route::get('user.index', [UserController::class, 'index'])->name('user.index');
Route::get('user.create', [UserController::class, 'create'])->name('user.create');
Route::post('user/store', [UserController::class, 'store'])->name('user.store');
Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

Route::get('category.index', [CategoriesController::class, 'index'])->name('category.index');
Route::get('category.create', [CategoriesController::class, 'create'])->name('category.create');
Route::post('category/store', [CategoriesController::class, 'store'])->name('category.store');
Route::get('category/{categories}/edit', [CategoriesController::class, 'edit'])->name('category.edit');
Route::put('category/{categories}', [CategoriesController::class, 'update'])->name('category.update');
Route::delete('category/{categories}', [CategoriesController::class, 'destroy'])->name('category.destroy');