@extends('layouts.app')

@section('title', 'Регистрация успешна')

@section('content')
<div style="max-width: 600px; margin: 50px auto; background: white; padding: 50px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
    
    
    <h2 style="color: #27ae60; margin-bottom: 20px;">Отлично! Вы зарегистрированы!</h2>
    
    <p style="font-size: 16px; color: #666; margin-bottom: 30px; line-height: 1.6;">
        Ваша регистрация прошла успешно!<br>
        Теперь вы можете авторизоваться и пользоваться всеми возможностями сайта.
    </p>
    
    <a href="{{ route('login') }}" class="btn btn-success" style="padding: 15px 40px; font-size: 16px; text-decoration: none; display: inline-block;">
        Войти в систему
    </a>
</div>
@endsection