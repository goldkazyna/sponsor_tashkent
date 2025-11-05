<?php

use Illuminate\Support\Facades\Route;

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