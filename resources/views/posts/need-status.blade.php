@extends('layouts.app')

@section('title', 'Необходим статус спонсора')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.need-status-container {
    max-width: 600px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.need-status-card {
    background: white;
    border-radius: 16px;
    padding: 3rem 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
}

.need-status-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.need-status-icon svg {
    width: 40px;
    height: 40px;
    fill: #d97706;
}

.need-status-card h1 {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.need-status-card p {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.need-status-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.btn-buy-status {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-buy-status:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
}

.btn-buy-status svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.btn-back {
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.btn-back:hover {
    color: #1a202c;
    text-decoration: none;
}

@media (max-width: 768px) {
    .need-status-container {
        padding: 0;
    }

    .need-status-card {
        padding: 2rem 1.5rem;
    }

    .need-status-card h1 {
        font-size: 1.3rem;
    }
}
</style>

<div class="need-status-container">
    <div class="need-status-card">
        <div class="need-status-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M17.13,17C15.92,18.85 14.11,20.24 12,20.92C9.89,20.24 8.08,18.85 6.87,17C6.53,16.5 6.24,16 6,15.47C6,13.82 8.71,12.47 12,12.47C15.29,12.47 18,13.79 18,15.47C17.76,16 17.47,16.5 17.13,17Z"/>
            </svg>
        </div>
        <h1>Необходим статус спонсора</h1>
        <p>
            Для добавления объявления мужчинам необходимо приобрести статус проверенного спонсора.
            Это подтверждает вашу серьёзность и повышает доверие к вашему профилю.
        </p>
        <div class="need-status-actions">
            <a href="{{ route('become.verified') }}" class="btn-buy-status">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
                Купить статус спонсора
            </a>
            <a href="{{ route('home') }}" class="btn-back">Вернуться на главную</a>
        </div>
    </div>
</div>

@endsection
