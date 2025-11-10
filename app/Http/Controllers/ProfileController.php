<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // Главная страница профиля (перенаправляет на мои объявления)
    public function index()
    {
        return redirect()->route('profile.posts');
    }

    // Мои объявления
    public function myPosts()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем объявления пользователя
        $posts = DB::table('posts')
            ->where('email', $user->email)
            ->where('del', 0)
            ->orderBy('date', 'desc')
            ->get();

        // Для каждого поста получаем первое фото
        foreach ($posts as $post) {
            $post->cover_img = DB::table('gallery')
                ->where('id_post', $post->id)
                ->first();
        }

        return view('profile.index', [
            'user' => $user,
            'posts' => $posts,
            'activeSection' => 'posts',
            'sectionTitle' => 'Мои объявления'
        ]);
    }

    // Сообщения
    public function messages()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'messages',
            'sectionTitle' => 'Сообщения'
        ]);
    }

    // Настройки профиля
    public function settings()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'settings',
            'sectionTitle' => 'Настройки профиля'
        ]);
    }

    // Расценки сайта
    public function pricing()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'pricing',
            'sectionTitle' => 'Расценки сайта'
        ]);
    }
}