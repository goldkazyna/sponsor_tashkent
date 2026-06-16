@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div style="max-width: 500px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px; color: #222;">Регистрация</h2>
    
    @if($errors->any())
        <div style="background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="list-style: none; padding: 0; margin: 0; color: #c00;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom: 5px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Пароль:</label>
            <input type="password" name="password" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Подтверждение пароля:</label>
            <input type="password" name="password_confirmation" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

		<div style="margin-bottom: 30px;">
			<label style="display: block; margin-bottom: 8px; font-weight: 500;">Пол:</label>
			<div style="display: flex; gap: 20px;">
				<label style="display: flex; align-items: center; cursor: pointer;">
					<input type="radio" name="sex" value="1" {{ old('sex') == 1 ? 'checked' : '' }} required style="margin-right: 8px;">
					<span>Мужской</span>
				</label>
				<label style="display: flex; align-items: center; cursor: pointer;">
					<input type="radio" name="sex" value="2" {{ old('sex') == 2 ? 'checked' : '' }} required style="margin-right: 8px;">
					<span>Женский</span>
				</label>
			</div>
		</div>

        <div style="display:flex; align-items:flex-start; gap:10px; margin-bottom: 18px;">
            <input type="checkbox" name="agree" id="agree" value="1" required {{ old('agree') ? 'checked' : '' }}
                   style="margin-top:3px; width:18px; height:18px; flex-shrink:0; cursor:pointer;">
            <label for="agree" style="font-size:0.9rem; color:#475569; line-height:1.5; cursor:pointer;">
                Я принимаю <a href="{{ route('agreement') }}" target="_blank" style="color:#e74c3c; font-weight:600;">Пользовательское соглашение</a>
                и <a href="{{ route('rules') }}" target="_blank" style="color:#e74c3c; font-weight:600;">Правила сайта</a>, и мне есть 18 лет
            </label>
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-size: 16px;">
            Зарегистрироваться
        </button>

        <p style="text-align: center; margin-top: 20px;">
            Уже есть аккаунт? <a href="{{ route('login') }}" style="color: #e74c3c; text-decoration: none;">Войти</a>
        </p>
    </form>
</div>
@endsection