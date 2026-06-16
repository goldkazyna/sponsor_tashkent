@extends('layouts.app')

@section('title', 'Инструкция по оплате')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ filemtime(public_path('css/cabinet.css')) }}">

<style>
.instruction-container {
    max-width: 800px;
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

.instruction-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.instruction-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.instruction-header p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.instruction-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
}

.instruction-card h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-radius: 50%;
    font-size: 0.9rem;
    font-weight: 700;
    flex-shrink: 0;
}

.instruction-card p {
    color: #475569;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 1rem;
}

.instruction-card p:last-child {
    margin-bottom: 0;
}

.instruction-card img {
    width: 100%;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    margin: 1rem 0;
}

.instruction-note {
    background: #fffbeb;
    border: 2px solid #fcd34d;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.instruction-note svg {
    width: 24px;
    height: 24px;
    fill: #d97706;
    flex-shrink: 0;
    margin-top: 2px;
}

.instruction-note p {
    color: #92400e;
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0;
}

.instruction-footer {
    text-align: center;
    margin-top: 2rem;
}

.instruction-footer a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 2rem;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.instruction-footer a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.4);
}

@media (max-width: 768px) {
    .instruction-container {
        padding: 0 1rem;
    }

    .instruction-header h1 {
        font-size: 1.4rem;
    }

    .instruction-card {
        padding: 1.5rem;
    }
}
</style>

<div class="instruction-container">

    <a href="{{ route('become.verified') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        Назад к тарифам
    </a>

    <div class="instruction-header">
        <h1>Инструкция по оплате</h1>
        <p>Пошаговая инструкция как оплатить статус проверенного пользователя или поднять объявление в ТОП</p>
    </div>

    <div class="instruction-note">
        <svg viewBox="0 0 24 24"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>
        <p>Если у вас возникли трудности с оплатой, напишите нам в Telegram — <a href="https://t.me/Sponsor_admin" target="_blank" style="color:#d97706;font-weight:600;">@Sponsor_admin</a>. Мы поможем!</p>
    </div>

    <div class="instruction-card">
        <h2><span class="step-number">1</span> Выберите тариф</h2>
        <p>На странице оплаты выберите подходящий тариф (5, 10 или 30 дней) и нажмите кнопку <strong>«Оплатить»</strong>.</p>
        <img src="{{ asset('images/instruction/step1.PNG') }}" alt="Шаг 1 — Выбор тарифа">
    </div>

    <div class="instruction-card">
        <h2><span class="step-number">2</span> Дождитесь загрузки страницы оплаты</h2>
        <p>Дождитесь когда система прогрузится. Оплата идёт только через <strong>Kaspi</strong>. Вам покажется страница с реквизитами — переведите деньги по указанному номеру и сумму которая указана выше.</p>
        <div class="instruction-note" style="margin-top:1rem;">
            <svg viewBox="0 0 24 24"><path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg>
            <p><strong>Важно:</strong> сумма перевода должна точно совпадать с указанной суммой. Иначе платёж не будет засчитан.</p>
        </div>
        <img src="{{ asset('images/instruction/step2.png') }}" alt="Шаг 2 — Страница реквизитов">
    </div>

    <div class="instruction-card">
        <h2><span class="step-number">3</span> Подтвердите оплату</h2>
        <p>После того как вы перевели деньги, нажмите кнопку <strong>«Подтвердить»</strong>. Система проверит статус транзакции.</p>
        <img src="{{ asset('images/instruction/step3.PNG') }}" alt="Шаг 3 — Подтверждение оплаты">
    </div>

    <div class="instruction-card">
        <h2><span class="step-number">4</span> Как перевести через Kaspi</h2>
        <p>Если вы не знаете как сделать перевод, следуйте этим шагам:</p>
        <ol style="color:#475569; font-size:0.95rem; line-height:2; padding-left:1.25rem; margin:0.75rem 0;">
            <li>Зайдите в мобильное приложение <strong>Kaspi Bank</strong></li>
            <li>Нажмите раздел <strong>«Переводы»</strong></li>
            <li>Выберите <strong>«Международные переводы»</strong></li>
            <li>Введите номер карты получателя</li>
            <li>Введите ФИО получателя</li>
            <li>Введите сумму перевода</li>
            <li>Подтвердите перевод</li>
        </ol>
        <img src="{{ asset('images/instruction/step4.PNG') }}" alt="Шаг 4 — Инструкция Kaspi">
    </div>

    <div class="instruction-footer">
        <a href="{{ route('become.verified') }}">Перейти к оплате</a>
    </div>

</div>

@endsection
