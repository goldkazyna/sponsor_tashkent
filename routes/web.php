<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    // Тестовые данные
    $users = [
        [
            'name' => 'Алина',
            'age' => 23,
            'city' => 'Ташкент',
            'type' => 'Содержанка',
            'emoji' => '👩'
        ],
        [
            'name' => 'Джамиля',
            'age' => 21,
            'city' => 'Ташкент',
            'type' => 'Содержанка',
            'emoji' => '💃'
        ],
        [
            'name' => 'Равшан',
            'age' => 35,
            'city' => 'Ташкент',
            'type' => 'Спонсор',
            'emoji' => '🤵'
        ],
        [
            'name' => 'Камила',
            'age' => 24,
            'city' => 'Самарканд',
            'type' => 'Содержанка',
            'emoji' => '👸'
        ],
        [
            'name' => 'Тимур',
            'age' => 42,
            'city' => 'Ташкент',
            'type' => 'Спонсор',
            'emoji' => '👨‍💼'
        ],
        [
            'name' => 'Малика',
            'age' => 22,
            'city' => 'Бухара',
            'type' => 'Содержанка',
            'emoji' => '💋'
        ],
    ];

    return view('home', ['users' => $users]);
});

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

Route::get('/add', [PostController::class, 'create'])->name('post.create');
Route::post('/add', [PostController::class, 'store'])->name('post.store');