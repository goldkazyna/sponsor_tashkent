@extends('layouts.app')

@section('title', 'Купить статус проверенного пользователя')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.verified-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
}

.back-button:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.back-button svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.verified-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.verified-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.verified-header p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.tariffs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.tariff-card {
    background: white;
    border-radius: 16px;
    padding: 2rem 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
    position: relative;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.tariff-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.tariff-card.popular {
    border-color: #3b82f6;
}

.tariff-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.tariff-days {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.tariff-days-label {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.tariff-price {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.25rem;
}

.tariff-price-usd {
    color: #94a3b8;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
}

.tariff-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem;
    text-align: left;
}

.tariff-features li {
    padding: 0.4rem 0;
    font-size: 0.85rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tariff-features li svg {
    width: 16px;
    height: 16px;
    fill: #22c55e;
    flex-shrink: 0;
}

.tariff-btn {
    width: 100%;
    padding: 0.85rem 1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    background: #1a202c;
}

.tariff-btn:hover {
    background: #2d3748;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(26,32,44,0.3);
}

.tariff-card.popular .tariff-btn {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.tariff-card.popular .tariff-btn:hover {
    box-shadow: 0 4px 12px rgba(59,130,246,0.4);
}

.tariff-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.verified-info {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.verified-info h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.info-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
    color: #475569;
}

.info-list li svg {
    width: 20px;
    height: 20px;
    fill: #3b82f6;
    flex-shrink: 0;
}

.error-message {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.help-form-wrapper {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-top: 2rem;
}

.help-form-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.help-form-header h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.help-form-header p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #1a202c;
    box-shadow: 0 0 0 3px rgba(26,32,44,0.1);
}

.form-input:read-only {
    background: #f8fafc;
    color: #64748b;
    cursor: not-allowed;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-helper {
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.submit-button {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.submit-button svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.success-message,
.error-message-form {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: none;
}

.success-message {
    background: #d1fae5;
    border: 2px solid #6ee7b7;
    color: #065f46;
}

.error-message-form {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
}

/* Кнопка инструкции */
.instruction-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.4rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.instruction-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.4);
}

.instruction-btn svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.instruction-center {
    text-align: center;
    margin-bottom: 2rem;
}

/* Модалка-оверлей */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-overlay.active {
    display: flex;
}

.modal-box {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    max-width: 550px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #94a3b8;
    line-height: 1;
}

.modal-close:hover {
    color: #1a202c;
}

.modal-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
    text-align: center;
}

.modal-text {
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.modal-text ol {
    padding-left: 1.25rem;
    margin: 0.75rem 0;
}

.modal-text ol li {
    margin-bottom: 0.5rem;
}

.modal-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.modal-btn-primary {
    width: 100%;
    padding: 0.85rem 1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.modal-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.4);
}

.modal-btn-secondary {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #64748b;
    background: white;
    text-align: center;
}

.modal-btn-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1a202c;
}

@media (max-width: 768px) {
    .verified-container {
        padding: 0 1rem;
    }

    .tariffs-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .info-list {
        grid-template-columns: 1fr;
    }

    .verified-header h1 {
        font-size: 1.4rem;
    }

    .help-form-wrapper {
        padding: 1.75rem;
    }

    .modal-box {
        padding: 1.5rem;
    }
}
</style>

<div class="verified-container">

    <a href="{{ route('home') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        На главную
    </a>

    <div class="verified-header">
        <h1>Купить статус проверенного пользователя</h1>
        <p>Получите значок проверенного пользователя и повысьте доверие к вашему профилю. Выберите подходящий тариф:</p>
    </div>

    {{-- Кнопка «Инструкция по оплате» скрыта, пока онлайн-оплата не работает. Восстановить — раскомментировать. --}}
    {{--
    <div class="instruction-center">
        <a href="{{ route('payment.instruction') }}" class="instruction-btn">
            <svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/></svg>
            Инструкция по оплате
        </a>
    </div>
    --}}

    @if(session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    {{-- Блок «Важно при оплате» (Kaspi) скрыт, пока онлайн-оплата не работает. Восстановить — раскомментировать. --}}
    {{--
    <div style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: #ef4444; flex-shrink: 0; margin-top: 2px;"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"/></svg>
            <div>
                <div style="font-weight: 700; color: #991b1b; font-size: 1rem; margin-bottom: 0.5rem;">Важно при оплате!</div>
                <ul style="margin: 0; padding-left: 1.25rem; color: #991b1b; font-size: 0.9rem; line-height: 1.7;">
                    <li><strong>Оплачивайте строго ту сумму, которая указана</strong> — не больше и не меньше, иначе платёж не будет засчитан.</li>
                    <li><strong>Оплата производится через Kaspi.</strong> Откройте своё приложение Kaspi и переведите на номер телефона, который будет показан после нажатия на кнопку «Оплатить». Каждый раз номер может быть разным — это платёжная организация меняет реквизиты.</li>
                </ul>
            </div>
        </div>
    </div>
    --}}

    {{-- Баннер: онлайн-оплата может сбоить — пробовать несколько раз, иначе ручной перевод через админа --}}
    <div style="background:#fffbeb; border:3px solid #f59e0b; border-radius:14px; padding:1.75rem 1.5rem; margin-bottom:1.5rem; text-align:center;">
        <div style="font-weight:800; color:#b45309; font-size:1.5rem; line-height:1.25; margin-bottom:0.85rem; letter-spacing:0.3px;">⚠️ Если оплата не проходит</div>
        <div style="color:#92400e; font-size:1.05rem; line-height:1.65; font-weight:600;">
            Иногда онлайн-оплата сбоит. Нажмите кнопку «Оплатить» <strong>несколько раз</strong>, пока система не выдаст реквизиты для перевода.<br>
            Если оплатить так и не получилось — напишите мне, оплатить можно <strong>криптовалютой</strong> или <strong>переводом на карту</strong> (только тариф на 1 месяц):<br>
            <a href="https://t.me/Sponsor_admin" target="_blank" style="display:inline-block; margin-top:0.5rem; color:#b45309; font-weight:800; font-size:1.2rem; text-decoration:underline;">@Sponsor_admin</a>
        </div>
    </div>

    <div class="tariffs-grid">

        {{-- 5 дней --}}
        <div class="tariff-card">
            <div class="tariff-days">5</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">7 592 ₸</div>
            <div class="tariff-price-usd">~ $14</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Значок проверенного
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Повышенное доверие
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Больше откликов
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="7592">
                <input type="hidden" name="days" value="5">
                <input type="hidden" name="service" value="verified_status">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

        {{-- 10 дней --}}
        <div class="tariff-card">
            <div class="tariff-days">10</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">10 846 ₸</div>
            <div class="tariff-price-usd">~ $20</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Значок проверенного
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Повышенное доверие
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Больше откликов
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="10846">
                <input type="hidden" name="days" value="10">
                <input type="hidden" name="service" value="verified_status">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

        {{-- 30 дней --}}
        <div class="tariff-card popular">
            <div class="tariff-badge">Популярный</div>
            <div class="tariff-days">30</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">16 268 ₸</div>
            <div class="tariff-price-usd">~ $30</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Значок проверенного
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Повышенное доверие
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg>
                    Максимальная выгода
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="16268">
                <input type="hidden" name="days" value="30">
                <input type="hidden" name="service" value="verified_status">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

    </div>

    <div class="verified-info">
        <h3>Что даёт статус проверенного?</h3>
        <ul class="info-list">
            <li>
                <svg viewBox="0 0 24 24"><path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/></svg>
                Значок верификации на профиле
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.63L12,2L9.19,8.63L2,9.24L7.46,13.97L5.82,21L12,17.27Z"/></svg>
                Приоритет в результатах поиска
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5Z"/></svg>
                Повышенное доверие пользователей
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M20,16H6L4,18V4H20"/></svg>
                Больше откликов на объявления
            </li>
        </ul>
    </div>

    {{-- Форма помощи --}}
    <div class="help-form-wrapper">

        <div class="help-form-header">
            <h3>Нужна помощь с оплатой?</h3>
            <p>Заполните форму ниже или напишите напрямую нам в Telegram — <a href="https://t.me/Sponsor_admin" target="_blank" style="color: #3b82f6; font-weight: 600; text-decoration: none;">@Sponsor_admin</a></p>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message-form" id="errorMessage"></div>

        <form id="helpForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Имя</label>
                <input type="text" name="name" class="form-input" placeholder="Ваше имя" value="{{ $user->fio ?? '' }}">
            </div>

            <div class="form-group">
                <label class="form-label required">Email</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" value="{{ $user->email ?? '' }}" {{ $user ? 'readonly' : '' }} required>
                @if($user)
                    <div class="form-helper">Email из вашего профиля</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label required">Ваш Telegram</label>
                <input type="text" name="telegram" class="form-input" placeholder="@username" value="{{ ($user->telegram_username ?? null) ? '@' . $user->telegram_username : '' }}" required>
                <div class="form-helper">Укажите Telegram — мы свяжемся с вами для помощи</div>
            </div>

            <div class="form-group">
                <label class="form-label">Комментарий</label>
                <textarea name="message" class="form-textarea" placeholder="Опишите вашу проблему или вопрос..."></textarea>
            </div>

            <button type="submit" class="submit-button" id="submitBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
                Отправить
            </button>

        </form>

    </div>

</div>

{{-- Модалка: предложение изучить инструкцию --}}
<div class="modal-overlay" id="paymentInterceptModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('paymentInterceptModal').classList.remove('active')">&times;</button>
        <div class="modal-title">У нас новая система оплаты</div>
        <div class="modal-text">
            Рекомендуем ознакомиться с инструкцией по оплате, чтобы понять как проходит процесс и избежать возможных ошибок.
        </div>
        <div class="modal-buttons">
            <a href="{{ route('payment.instruction') }}" class="modal-btn-primary" style="text-decoration:none;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:white;"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/></svg>
                Посмотреть инструкцию
            </a>
            <button type="button" class="modal-btn-secondary" id="proceedPaymentBtn">
                Всё равно оплатить
            </button>
        </div>
    </div>
</div>

<script>
var sawInstruction = {{ ($user->saw_instruction ?? 0) ? 'true' : 'false' }};
var pendingForm = null;

document.querySelectorAll('.tariff-card form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!sawInstruction) {
            e.preventDefault();
            pendingForm = form;
            document.getElementById('paymentInterceptModal').classList.add('active');
        }
    });
});

document.getElementById('proceedPaymentBtn').addEventListener('click', function() {
    document.getElementById('paymentInterceptModal').classList.remove('active');
    if (pendingForm) {
        sawInstruction = true;
        pendingForm.submit();
    }
});

document.getElementById('paymentInterceptModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

document.getElementById('helpForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;animation:spin 1s linear infinite;"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Отправка...';

    const data = {
        name: this.querySelector('[name="name"]').value,
        email: this.querySelector('[name="email"]').value,
        telegram: this.querySelector('[name="telegram"]').value,
        message: this.querySelector('[name="message"]').value,
    };

    fetch('{{ route("become.verified.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok && response.status !== 422) {
            throw new Error('Network error');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            successMsg.textContent = data.message;
            successMsg.style.display = 'block';
            document.getElementById('helpForm').reset();
            successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            errorMsg.textContent = data.message || 'Произошла ошибка при отправке';
            errorMsg.style.display = 'block';
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .catch(error => {
        errorMsg.textContent = 'Произошла ошибка. Попробуйте позже.';
        errorMsg.style.display = 'block';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg> Отправить';
    });
});
</script>

@endsection
