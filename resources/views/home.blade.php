@extends('layouts.app')

@section('title', 'Главная - Спонсоры Ташкент')

@section('content')
    <h2 style="margin-bottom: 20px;">🌟 Добро пожаловать!</h2>
    
    <div style="background: #ecf0f1; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3>О нас</h3>
        <p>Лучший сайт знакомств для спонсоров и содержанок в Ташкенте.</p>
        <p>Найди свою идеальную пару уже сегодня!</p>
    </div>

    <h3 style="margin-bottom: 15px;">👥 Новые анкеты</h3>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        @foreach($users as $user)
        <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: white;">
            <div style="background: #3498db; color: white; width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 40px;">
                {{ $user['emoji'] }}
            </div>
            <h4 style="text-align: center; margin-bottom: 5px;">{{ $user['name'] }}</h4>
            <p style="text-align: center; color: #7f8c8d;">{{ $user['age'] }} лет, {{ $user['city'] }}</p>
            <p style="text-align: center; margin-top: 10px;">
                <span style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                    {{ $user['type'] }}
                </span>
            </p>
        </div>
        @endforeach
    </div>
@endsection