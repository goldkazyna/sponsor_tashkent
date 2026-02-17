@extends('layouts.app')

@section('title', 'Расценки на платные услуги')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.pricing-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.back-to-home {
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

.back-to-home:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.back-to-home svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.pricing-hero {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    margin-bottom: 3rem;
    color: white;
}

.pricing-hero h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.pricing-hero p {
    font-size: 0.95rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.section-header svg {
    width: 28px;
    height: 28px;
    fill: #64748b;
}

.section-header p {
    font-size: 0.9rem;
    color: #64748b;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 4rem;
}

.pricing-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
    border: 2px solid #e2e8f0;
}

.pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    border-color: #1a202c;
}

.card-icon {
    width: 55px;
    height: 55px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.card-icon svg {
    width: 28px;
    height: 28px;
    fill: #64748b;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.card-duration {
    display: inline-block;
    background: #f1f5f9;
    color: #64748b;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.card-price {
    margin-bottom: 1.5rem;
}

.price-main {
    font-size: 2.2rem;
    font-weight: 700;
    color: #1a202c;
    line-height: 1;
}

.price-currency {
    font-size: 1.3rem;
    color: #64748b;
    margin-left: 0.3rem;
}

.price-sub {
    font-size: 0.9rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.card-description {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 2rem;
    font-size: 0.9rem;
}

.card-features {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.card-features li {
    display: flex;
    align-items: start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: #64748b;
    font-size: 0.85rem;
}

.card-features li svg {
    width: 16px;
    height: 16px;
    fill: #64748b;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.card-button {
    display: block;
    width: 100%;
    padding: 0.9rem;
    background: #1a202c;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.card-button:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26,32,44,0.3);
    color: white;
    text-decoration: none;
}

.contact-section {
    background: white;
    border-radius: 16px;
    padding: 1.75rem 2rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 2px solid #e2e8f0;
}

.contact-section h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.contact-section p {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 1.25rem;
}

.telegram-link {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: #1a202c;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.telegram-link:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26,32,44,0.3);
    text-decoration: none;
    color: white;
}

.telegram-icon {
    width: 20px;
    height: 20px;
}

@media (max-width: 992px) {
    .pricing-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .pricing-hero {
        padding: 1.75rem 1.5rem;
    }

    .pricing-hero h1 {
        font-size: 1.5rem;
    }

    .pricing-hero p {
        font-size: 0.9rem;
    }

    .section-header h2 {
        font-size: 1.3rem;
    }

    .pricing-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .pricing-card {
        padding: 1.5rem;
    }

    .contact-section {
        padding: 1.5rem;
    }

    .contact-section h2 {
        font-size: 1.1rem;
    }
}
</style>

<div class="pricing-container">
    
    <!-- Кнопка назад -->
    <a href="{{ route('home') }}" class="back-to-home">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        На главную
    </a>

    <!-- Hero секция -->
    <div class="pricing-hero">
        <h1>Платные услуги</h1>
        <p>Все платежи единоразовые, без подписки. Выбирайте тариф на нужное количество дней</p>
    </div>

    <!-- Проверенный пользователь -->
    <div class="section-header">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
            </svg>
            Проверенный пользователь
        </h2>
        <p>Получите значок доверия и выделитесь среди остальных пользователей</p>
    </div>

    <div class="pricing-grid">
        
        <!-- 5 дней -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
            </div>
            <h3 class="card-title">Проверенный пользователь на 5 дней</h3>
            <div class="card-duration">5 дней</div>
            <div class="card-price">
                <div class="price-main">14<span class="price-currency">$</span></div>
                <div class="price-sub">7592 тг</div>
            </div>
            <p class="card-description">
                Ваш аккаунт станет идентифицированным на 5 дней
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Иконка на всех объявлениях
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше доверия от пользователей
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Выделение в поиске
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

        <!-- 10 дней -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
            </div>
            <h3 class="card-title">Проверенный пользователь на 10 дней</h3>
            <div class="card-duration">10 дней</div>
            <div class="card-price">
                <div class="price-main">20<span class="price-currency">$</span></div>
                <div class="price-sub">10 846 тг</div>
            </div>
            <p class="card-description">
                Ваш аккаунт станет идентифицированным на 10 дней
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Иконка на всех объявлениях
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше доверия от пользователей
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Выделение в поиске
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

        <!-- 1 месяц -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
            </div>
            <h3 class="card-title">Проверенный пользователь на 1 месяц</h3>
            <div class="card-duration">30 дней</div>
            <div class="card-price">
                <div class="price-main">30<span class="price-currency">$</span></div>
                <div class="price-sub">16 268 тг</div>
            </div>
            <p class="card-description">
                Ваш аккаунт станет идентифицированным на месяц
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Иконка на всех объявлениях
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше доверия от пользователей
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Выделение в поиске
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

    </div>

    <!-- Топ объявления -->
    <div class="section-header">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M16,17V19H2V17S2,13 9,13 16,17 16,17M12.5,7.5A3.5,3.5 0 0,1 9,11A3.5,3.5 0 0,1 5.5,7.5A3.5,3.5 0 0,1 9,4A3.5,3.5 0 0,1 12.5,7.5M15.94,13A5.32,5.32 0 0,1 18,17V19H22V17S22,13.37 15.94,13M15,4A3.39,3.39 0 0,0 13.07,4.59A5,5 0 0,1 13.07,10.41A3.39,3.39 0 0,0 15,11A3.5,3.5 0 0,0 18.5,7.5A3.5,3.5 0 0,0 15,4Z"/>
            </svg>
            Топ объявления
        </h2>
        <p>Увеличьте количество показов и откликов в 10-15 раз</p>
    </div>

    <div class="pricing-grid">
        
        <!-- Топ 5 дней -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                </svg>
            </div>
            <h3 class="card-title">Топ объявление на 5 дней</h3>
            <div class="card-duration">5 дней</div>
            <div class="card-price">
                <div class="price-main">14<span class="price-currency">$</span></div>
                <div class="price-sub">7592 тг</div>
            </div>
            <p class="card-description">
                Ваше объявление будет в топе 5 дней
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Размещение в топе списка
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Увеличение показов в 10-15 раз
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше откликов
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

        <!-- Топ 10 дней -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                </svg>
            </div>
            <h3 class="card-title">Топ объявление на 10 дней</h3>
            <div class="card-duration">10 дней</div>
            <div class="card-price">
                <div class="price-main">20<span class="price-currency">$</span></div>
                <div class="price-sub">10 846 тг</div>
            </div>
            <p class="card-description">
                Ваше объявление будет в топе 10 дней
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Размещение в топе списка
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Увеличение показов в 10-15 раз
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше откликов
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

        <!-- Топ 30 дней -->
        <div class="pricing-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                </svg>
            </div>
            <h3 class="card-title">Топ объявление на 30 дней</h3>
            <div class="card-duration">30 дней</div>
            <div class="card-price">
                <div class="price-main">30<span class="price-currency">$</span></div>
                <div class="price-sub">16 268 тг</div>
            </div>
            <p class="card-description">
                Ваше объявление будет в топе 30 дней
            </p>
            <ul class="card-features">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Размещение в топе списка
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Увеличение показов в 10-15 раз
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    Больше откликов
                </li>
            </ul>
            <a href="#" class="card-button">Оплатить</a>
        </div>

    </div>

    <!-- Контактная секция -->
    <div class="contact-section">
        <h2>Остались вопросы?</h2>
        <p>Свяжитесь с нами в Telegram</p>
        <a href="https://t.me/SPONSOR_ADMIN" target="_blank" class="telegram-link">
            <svg class="telegram-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M16.64,7.8C16.8,7.8 16.94,7.85 17.03,7.95C17.13,8.05 17.16,8.19 17.13,8.32L15.89,15.21C15.84,15.5 15.63,15.68 15.34,15.68C15.21,15.68 15.08,15.64 14.97,15.57L12.19,13.54L10.79,14.91C10.68,15.03 10.53,15.09 10.38,15.09C10.23,15.09 10.08,15.03 9.97,14.91L10.26,12.08L15.35,7.73C15.39,7.69 15.39,7.64 15.35,7.6C15.32,7.56 15.26,7.55 15.21,7.58L8.69,11.62L5.95,10.7C5.67,10.6 5.53,10.42 5.53,10.15C5.53,9.88 5.67,9.7 5.95,9.6L15.84,6.15C16,6.09 16.16,6.06 16.32,6.06C16.43,6.06 16.54,6.08 16.64,6.12V7.8Z"/>
            </svg>
            @SPONSOR_ADMIN
        </a>
    </div>

</div>

@endsection