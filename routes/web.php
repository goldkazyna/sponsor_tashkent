<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

// Главная страница со списком объявлений
Route::get('/', [PostController::class, 'index'])->name('home');

// Регистрация
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Авторизация
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Выход
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Восстановление пароля
Route::get('/password/reset', [AuthController::class, 'showResetRequest'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{code}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/update', [AuthController::class, 'resetPassword'])->name('password.update');

// Объявления
Route::get('/add', [PostController::class, 'create'])->name('post.create');
Route::post('/add', [PostController::class, 'store'])->name('post.store');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('post.detail');