@extends('layouts.app')

@section('title', 'Оплата прошла успешно')

@section('content')

<style>
.payment-result {
    max-width: 600px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.payment-card {
    background: white;
    border-radius: 16px;
    padding: 3rem 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.payment-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: #d1fae5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-icon svg {
    width: 40px;
    height: 40px;
    fill: #059669;
}

.payment-card h1 {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.payment-card p {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.payment-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: #1a202c;
    color: white;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.payment-btn:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26,32,44,0.3);
    color: white;
    text-decoration: none;
}

@media (max-width: 768px) {
    .payment-result { padding: 0; }
    .payment-card { padding: 2rem 1.5rem; }
    .payment-card h1 { font-size: 1.3rem; }
}
</style>

<div class="payment-result">
    <div class="payment-card">
        <div class="payment-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
            </svg>
        </div>
        <h1>Оплата прошла успешно!</h1>
        <p>Спасибо за оплату. Ваш статус проверенного пользователя активирован.</p>
        <a href="{{ route('home') }}" class="payment-btn">Вернуться на главную</a>
    </div>
</div>

@endsection
