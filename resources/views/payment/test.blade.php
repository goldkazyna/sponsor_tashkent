@extends('layouts.app')

@section('title', 'Тест оплаты')

@section('content')

<style>
.test-payment {
    max-width: 500px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.test-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.test-card h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1.5rem;
    text-align: center;
}

.test-info {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    color: #475569;
    line-height: 1.6;
}

.test-info strong {
    color: #1a202c;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-input {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1a202c;
    box-shadow: 0 0 0 3px rgba(26,32,44,0.1);
}

.btn-pay {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-pay:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(5,150,105,0.4);
}

.error-msg {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .test-payment { padding: 0; }
    .test-card { padding: 1.75rem; }
}
</style>

<div class="test-payment">
    <div class="test-card">
        <h1>Тестовая оплата</h1>

        @if(session('error'))
            <div class="error-msg">{{ session('error') }}</div>
        @endif

        <div class="test-info">
            <strong>Пользователь:</strong> {{ $user->email }}<br>
            <strong>ID:</strong> {{ $user->id }}
        </div>

        <form method="POST" action="{{ route('payment.create') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Сумма (KZT)</label>
                <input type="number" name="amount" class="form-input" value="100" min="1">
            </div>

            <button type="submit" class="btn-pay">Оплатить</button>
        </form>
    </div>
</div>

@endsection
