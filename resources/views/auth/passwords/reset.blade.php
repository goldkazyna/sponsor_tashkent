@extends('layouts.app')

@section('title', 'Сброс пароля')

@section('content')
<div style="max-width: 500px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px; color: #222;">Новый пароль</h2>
    
    @if($errors->any())
        <div style="background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="list-style: none; padding: 0; margin: 0; color: #c00;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom: 5px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="code" value="{{ $code }}">
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Новый пароль:</label>
            <input type="password" name="password" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Подтверждение пароля:</label>
            <input type="password" name="password_confirmation" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-size: 16px;">
            Сменить пароль
        </button>
    </form>
</div>
@endsection