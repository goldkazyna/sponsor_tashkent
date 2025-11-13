<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;

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

// Личный кабинет (требует авторизации)
Route::middleware(['web'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/posts', [ProfileController::class, 'myPosts'])->name('profile.posts');
    Route::get('/profile/messages', [ProfileController::class, 'messages'])->name('profile.messages');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/pricing', [ProfileController::class, 'pricing'])->name('profile.pricing');
    
    // Управление объявлениями
    Route::get('/profile/post/edit/{id}', [ProfileController::class, 'editPost'])->name('profile.post.edit');
    Route::post('/profile/post/update/{id}', [ProfileController::class, 'updatePost'])->name('profile.post.update');
    Route::post('/profile/post/delete/{id}', [ProfileController::class, 'deletePost'])->name('profile.post.delete');
    Route::post('/profile/photo/delete/{id}', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
	Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
	Route::post('/profile/password/update', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});