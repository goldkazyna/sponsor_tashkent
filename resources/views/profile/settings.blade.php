@extends('layouts.app')

@section('title', 'Настройки профиля')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ filemtime(public_path('css/cabinet.css')) }}">

<style>
.settings-container {
    max-width: 1200px;
    margin: 0 auto;
}

.settings-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.settings-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.settings-header p {
    color: #64748b;
    font-size: 0.9rem;
}

.settings-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title svg {
    width: 24px;
    height: 24px;
    fill: #1a202c;
}

.section-description {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1a202c;
}

.form-input:disabled {
    background: #f8fafc;
    cursor: not-allowed;
    color: #94a3b8;
}

.btn-save {
    padding: 0.8rem 2rem;
    background: #1a202c;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26, 32, 44, 0.3);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.alert-success {
    background: #dcfce7;
    border: 2px solid #86efac;
    color: #166534;
}

.alert-error {
    background: #fee2e2;
    border: 2px solid #fecaca;
    color: #dc2626;
}

.info-box {
    background: #f0f9ff;
    border: 2px solid #bae6fd;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.info-box p {
    color: #0c4a6e;
    font-size: 0.9rem;
    margin: 0;
}

.divider {
    height: 2px;
    background: #f1f5f9;
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .settings-header {
        padding: 1.2rem;
    }

    .settings-header h1 {
        font-size: 1.4rem;
    }

    .settings-section {
        padding: 1.5rem;
    }

    .section-title {
        font-size: 1.1rem;
    }
}
</style>

<div class="settings-container">
    
    <!-- Шапка -->
    <div class="settings-header">
        <h1>Настройки профиля</h1>
        <p>Управление вашим аккаунтом</p>
    </div>

    <!-- Сообщения об успехе/ошибках -->
    @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Секция: Основная информация -->
    <div class="settings-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
            </svg>
            Основная информация
        </div>
        <div class="section-description">
            Эта информация будет использоваться в ваших объявлениях
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Email адрес</label>
                <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                <small style="color: #94a3b8; font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                    Email изменить нельзя
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Телефон</label>
                <input type="text" name="phone" class="form-input" value="{{ $user->phone ?? '' }}" placeholder="+7 700 123 45 67">
                <small style="color: #94a3b8; font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                    Этот номер будет использоваться по умолчанию при создании объявлений
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Telegram</label>
                <div style="position: relative;">
                    <input type="text" name="telegram_username" class="form-input" value="{{ $user->telegram_username ?? '' }}" placeholder="username" style="padding-left: 2.5rem;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 600;">@</span>
                </div>
                <small style="color: #94a3b8; font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                    Введите ваш Telegram username без @
                </small>
            </div>

            <div class="info-box">
                <p>
                    <strong>Тип аккаунта:</strong> 
                    {{ $user->sex == 1 ? '👨 Мужчина' : '👩 Женщина' }}
                </p>
            </div>

            <button type="submit" class="btn-save">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 6px;">
                    <path d="M15,9H5V5H15M12,19A3,3 0 0,1 9,16A3,3 0 0,1 12,13A3,3 0 0,1 15,16A3,3 0 0,1 12,19M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3Z"/>
                </svg>
                Сохранить изменения
            </button>
        </form>
    </div>

    <div class="divider"></div>

    <!-- Секция: Безопасность -->
    <div class="settings-section">
        <div class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
            </svg>
            Безопасность
        </div>
        <div class="section-description">
            Смените пароль, чтобы защитить свой аккаунт
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Текущий пароль</label>
                <input type="password" name="current_password" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Новый пароль</label>
                <input type="password" name="new_password" class="form-input" required>
                <small style="color: #94a3b8; font-size: 0.85rem; display: block; margin-top: 0.5rem;">
                    Минимум 6 символов
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Подтверждение нового пароля</label>
                <input type="password" name="new_password_confirmation" class="form-input" required>
            </div>

            <button type="submit" class="btn-save">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 6px;">
                    <path d="M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                </svg>
                Изменить пароль
            </button>
        </form>
    </div>

    <!-- Дополнительная информация -->
    <div class="info-box" style="margin-top: 2rem;">
        <p style="text-align: center;">
            <strong>Нужна помощь?</strong> Свяжитесь с нами через раздел 
            <a href="/contact" style="color: #0c4a6e; text-decoration: underline;">Написать админу</a>
        </p>
    </div>

</div>

@endsection