@extends('layouts.app')

@section('title', 'Ошибка оплаты')

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
    background: #fee2e2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-icon svg {
    width: 40px;
    height: 40px;
    fill: #dc2626;
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

.payment-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
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

.payment-link {
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
}

.payment-link:hover {
    color: #1a202c;
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
                <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
            </svg>
        </div>
        <h1>Оплата не прошла</h1>
        <p>К сожалению, платёж не был завершён. Попробуйте ещё раз или свяжитесь с администрацией.</p>
        <div class="payment-actions">
            <a href="{{ route('pricing') }}" class="payment-btn">Попробовать снова</a>
            <a href="{{ route('home') }}" class="payment-link">Вернуться на главную</a>
        </div>
    </div>
</div>

@endsection
