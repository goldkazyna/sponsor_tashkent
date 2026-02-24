<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index()
    {
        $baseUrl = rtrim(config('app.url'), '/');

        // Статические страницы
        $static = [
            ['url' => '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => '/rules', 'changefreq' => 'monthly', 'priority' => '0.3'],
            ['url' => '/pricing', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['url' => '/contact', 'changefreq' => 'monthly', 'priority' => '0.3'],
            ['url' => '/register', 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['url' => '/login', 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['url' => '/become-verified', 'changefreq' => 'monthly', 'priority' => '0.4'],
        ];

        // Города — фильтрованные главные страницы
        $cities = DB::table('city')->orderBy('id')->get(['id']);

        // Активные объявления
        $posts = DB::table('post')
            ->where('del', 0)
            ->orderBy('id', 'desc')
            ->get(['id', 'date']);

        return response()
            ->view('sitemap', compact('baseUrl', 'static', 'cities', 'posts'))
            ->header('Content-Type', 'application/xml');
    }
}
