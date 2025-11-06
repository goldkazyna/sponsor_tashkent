@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
<div style="max-width: 500px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px; color: #222;">Восстановление пароля</h2>
    
    @if(session('success'))
        <div style="background: #efe; border: 1px solid #cfc; padding: 15px; border-radius: 5px; margin-bottom: 20px; color: #060;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="list-style: none; padding: 0; margin: 0; color: #c00;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom: 5px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p style="margin-bottom: 20px; color: #666;">Введите ваш email, и мы отправим вам ссылку для восстановления пароля.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 16px;">
            Отправить ссылку
        </button>

        <p style="text-align: center; margin-top: 20px;">
            <a href="{{ route('login') }}" style="color: #e74c3c; text-decoration: none;">Вернуться к входу</a>
        </p>
    </form>
</div>
@endsection