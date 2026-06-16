@extends('layouts.app')

@section('title', 'Продажа сервиса | Спонсоры и Содержанки Казахстан')

@section('meta_description', 'Действующий сервис знакомств (спонсоры и содержанки) в Казахстане продаётся. Готовый бизнес с трафиком, базой объявлений и Telegram-ботом.')

@section('content')

<style>
.sale-wrap {
    max-width: 820px;
    margin: 2rem auto 3rem;
    padding: 0 1rem;
}
.sale-hero {
    background: linear-gradient(135deg, #f5a623, #e8941a);
    border-radius: 18px 18px 0 0;
    padding: 2.5rem 2rem;
    text-align: center;
    color: #fff;
}
.sale-hero .badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 1rem;
}
.sale-hero h1 {
    font-size: 1.9rem;
    font-weight: 800;
    margin: 0 0 0.6rem;
    line-height: 1.25;
}
.sale-hero p {
    font-size: 1rem;
    opacity: 0.95;
    margin: 0;
    line-height: 1.6;
}
.sale-card {
    background: #fff;
    border-radius: 0 0 18px 18px;
    padding: 2.2rem 2rem;
    box-shadow: 0 6px 24px rgba(0,0,0,0.08);
}
.sale-card h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 1rem;
}
.sale-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem;
}
.sale-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 0.97rem;
    line-height: 1.5;
}
.sale-list li:last-child { border-bottom: none; }
.sale-list .ic {
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fef3e2;
    color: #d97706;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    margin-top: 1px;
}
.sale-price {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    background: linear-gradient(135deg, #fff7ed, #ffedd5);
    border: 2px solid #f5a623;
    border-radius: 16px;
    padding: 1.6rem 1.5rem;
    margin-bottom: 1.5rem;
}
.sale-price__label {
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #b45309;
}
.sale-price__value {
    font-size: 2.6rem;
    font-weight: 900;
    color: #d97706;
    line-height: 1;
}
.sale-cta {
    text-align: center;
    background: #fafafa;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    padding: 1.8rem 1.5rem;
}
.sale-cta p {
    color: #475569;
    margin: 0 0 1.2rem;
    font-size: 1rem;
}
.sale-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #0088cc, #0077b3);
    color: #fff;
    padding: 13px 28px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(0,136,204,0.35);
    transition: transform 0.15s;
}
.sale-btn:hover { transform: translateY(-2px); }
.sale-btn svg { width: 20px; height: 20px; fill: #fff; }
@media (max-width: 600px) {
    .sale-hero h1 { font-size: 1.5rem; }
    .sale-hero, .sale-card { padding: 1.8rem 1.3rem; }
}
</style>

<div class="sale-wrap">
    <div class="sale-hero">
        <span class="badge">🏷️ Продаётся</span>
        <h1>Готовый бизнес или доля в нём — на продажу</h1>
        <p>Действующий сервис знакомств с реальным трафиком, базой объявлений и автоматизацией. Можно купить как весь проект целиком, так и долю.</p>
    </div>

    <div class="sale-card">
        <h2>Что входит в продажу</h2>
        <ul class="sale-list">
            <li><span class="ic">✓</span> Сайт-площадка объявлений (спонсоры и содержанки) по Казахстану</li>
            <li><span class="ic">✓</span> Накопленная база объявлений и зарегистрированных пользователей</li>
            <li><span class="ic">✓</span> Telegram-бот: регистрация, объявления, ТОП, оплата статуса</li>
            <li><span class="ic">✓</span> Telegram-каналы: информационный, чёрный список, приём жалоб</li>
            <li><span class="ic">✓</span> Домен, исходный код и настроенная система модерации</li>
            <li><span class="ic">✓</span> Налаженный поисковый трафик (SEO) и monetization-модель</li>
        </ul>

        <div class="sale-price">
            <span class="sale-price__label">Можно купить долю</span>
            <span class="sale-price__value">от 10%</span>
        </div>

        <div class="sale-cta">
            <p>Покупайте бизнес целиком или его часть — хоть 10% доли.<br>Напишите администратору — обсудим условия, цену и передачу.</p>
            <a href="https://t.me/Sponsor_admin" target="_blank" rel="noopener" class="sale-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/></svg>
                Написать @Sponsor_admin
            </a>
        </div>
    </div>
</div>

@endsection
