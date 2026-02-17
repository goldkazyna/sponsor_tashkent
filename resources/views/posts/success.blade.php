@extends('layouts.app')

@section('title', 'Объявление добавлено')

@section('content')
<div style="max-width: 600px; margin: 50px auto; background: white; padding: 50px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
    <div style="font-size: 60px; margin-bottom: 20px;">✅</div>
    
    <h2 style="color: #27ae60; margin-bottom: 20px;">Спасибо! Объявление добавлено!</h2>
    
    <p style="font-size: 16px; color: #666; margin-bottom: 30px; line-height: 1.6;">
        Ваше объявление успешно опубликовано и уже доступно на сайте.<br>
        Вы можете просмотреть все объявления на главной странице.
    </p>
    
    <div style="display: flex; gap: 15px; justify-content: center;">
        <a href="/" class="btn btn-success" style="padding: 12px 30px; font-size: 16px; text-decoration: none; display: inline-block;">
            На главную
        </a>
        <a href="/post/detail/{{ $postId }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px; text-decoration: none; display: inline-block;">
            Посмотреть объявление
        </a>
    </div>
</div>
@endsection