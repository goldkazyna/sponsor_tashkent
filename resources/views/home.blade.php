@extends('layouts.app')

@section('title', 'Спонсоры и Содержанки в Алматы, Астане, Казахстане.')

@section('meta_description', 'Используйте VPN для доступа к сайту. Вы можете найти для себя спонсора или содержанки в Алматы, Астане и других городах на данном портале.')

@section('content')

<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<style>
.massage-wrapper-v2 {
    max-width: 1200px;
    margin: 0 auto;
}

.card-v2 {
    width: 100%;
    max-width: 1200px;
    margin-bottom: 20px;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
}

.card-v2:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.card-header-v2 {
    padding: 24px;
    border-bottom: 1px solid #f8fafc;
    background: #fefefe;
}

.header-content-v2 {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.photo-container {
    width: 193px;
    height: 193px;
    border: 2px solid #f8fafc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}

.photo-v2 {
    border-radius: 12px;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-placeholder-v2 {
    width: 193px;
    height: 193px;
    border-radius: 12px;
    border: 2px solid #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8fafc;
    text-align: center;
    color: #64748b;
    font-size: 12px;
    line-height: 1.4;
    padding: 10px;
}

.service-info-v2 {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.service-info-v2 h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 6px;
}

.service-info-v2 h3 a {
    color: #0f172a;
    text-decoration: none;
}

.service-info-v2 h3 a:hover {
    color: #7c3aed;
}

.specialist-v2 {
    color: #64748b;
    font-size: 14px;
    margin-bottom: 8px;
}

.user-type-v2 {
    color: #7c3aed;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 8px;
}

.location-date-v2 {
    color: #94a3b8;
    font-size: 12px;
    margin-bottom: 12px;
}

.description-v2 {
    color: #475569;
    font-size: 14px;
    line-height: 1.4;
    flex: 1;
}

.contact-panel-v2 {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-end;
    min-width: 150px;
}

.phone-v2 {
    background: #0f172a;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    width: 100%;
    text-align: center;
    display: block;
}

.phone-v2:hover {
    text-decoration: none;
    color: #ffffff;
    background: #7c7c7c;
}

.contact-buttons-v2 {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
}

.contact-btn-v2 {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    width: 100%;
    text-align: center;
    display: block;
}

.btn-whatsapp-v2 {
    background: #25d366;
    color: white;
}

.btn-whatsapp-v2:hover {
    background: #157b3b;
    color: white;
    text-decoration: none;
}

.btn-telegram-v2 {
    background: #0088cc;
    color: white;
}

.btn-telegram-v2:hover {
    background: #02547d;
    color: white;
    text-decoration: none;
}

/* Сообщение для неавторизованных */
.restricted-message {
    color: #dc2626;
    font-size: 12px;
    text-align: center;
    padding: 8px;
    background: #fef2f2;
    border-radius: 6px;
    border: 1px solid #fecaca;
}

.restricted-message a {
    color: #dc2626;
    text-decoration: underline;
}

/* Сообщение "купите статус" */
.restricted-sponsor {
    background: #fef3c7;
    border-color: #fcd34d;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
}

.restricted-sponsor svg {
    width: 18px;
    height: 18px;
    fill: #f59e0b;
    flex-shrink: 0;
}

.restricted-sponsor a {
    color: #92400e;
    font-weight: 600;
    text-decoration: underline;
}

.card-body-v2 {
    padding: 20px 24px;
}

.views-trusted-v2 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.views-v2 {
    color: #94a3b8;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.trusted-sponsor-v2 {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #059669;
    font-size: 11px;
    font-weight: 500;
}

/* Адаптивный дизайн */
@media (max-width: 1024px) {
    .massage-wrapper-v2 {
        max-width: 768px;
    }
    
    .card-header-v2 {
        padding: 20px;
    }
    
    .photo-container {
        width: 150px;
        height: 150px;
    }
    
    .photo-placeholder-v2 {
        width: 150px;
        height: 150px;
    }
}

@media (max-width: 768px) {
    .card-header-v2 {
        padding: 16px;
    }
    
    .header-content-v2 {
        gap: 14px;
    }
    
    .photo-container {
        width: 120px;
        height: 120px;
    }
    
    .photo-placeholder-v2 {
        width: 120px;
        height: 120px;
        font-size: 11px;
    }
}

@media (max-width: 576px) {
    .header-content-v2 {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .photo-container {
        width: 140px;
        height: 140px;
    }
    
    .photo-placeholder-v2 {
        width: 140px;
        height: 140px;
    }
    
    .service-info-v2 {
        width: 100%;
        text-align: center;
    }
    
    .contact-panel-v2 {
        width: 100%;
        align-items: center;
    }
    
    .description-v2 {
        text-align: left;
    }
}

/* Стили для пагинации */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 8px;
    align-items: center;
}

.pagination li {
    display: inline-block;
}

.pagination a,
.pagination span {
    display: block;
    padding: 10px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination a:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}

.pagination .active span {
    background: #34495e;
    border-color: #34495e;
    color: white;
}

.pagination .disabled span {
    color: #cbd5e1;
    cursor: not-allowed;
}

.pagination .disabled a:hover {
    background: white;
    border-color: #e2e8f0;
}

@media (max-width: 576px) {
    .pagination a,
    .pagination span {
        padding: 8px 12px;
        font-size: 13px;
    }
}

/* Курсор pointer для кликабельных фото */
.photo-clickable {
    cursor: pointer;
}

/* ===== ТОП ОБЪЯВЛЕНИЯ ===== */
.top-section-title {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.top-section-title span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 28px;
    border: 2px solid #1a202c;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    color: #1a202c;
    background: white;
}

.card-v2.card-top {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #eab308 100%);
    border: 2px solid #d97706;
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
}

.card-top .card-header-v2 {
    background: transparent;
    border-bottom: 1px solid rgba(255,255,255,0.3);
}

.card-top .card-body-v2 {
    background: rgba(0,0,0,0.03);
}

.card-top .service-info-v2 h3 a {
    color: #1a202c;
}

.card-top .service-info-v2 h3 a:hover {
    color: #78350f;
}

.card-top .description-v2 {
    color: #1c1917;
}

.card-top .specialist-v2 {
    color: #44403c;
}

.card-top .user-type-v2 {
    color: #78350f;
}

.card-top .location-date-v2 {
    color: #57534e;
}

.card-top .views-v2 {
    color: #57534e;
}

.card-top .trusted-sponsor-v2 {
    color: #065f46;
}

.top-badge {
    display: inline-block;
    background: #dc2626;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.top-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6px;
}

.top-header-row h3 {
    flex: 1;
    margin-right: 10px;
}
</style>

{{-- Уведомление о закрытии сервиса. Вернуть объявления — заменить @if(false) ниже на @if(true) и убрать этот блок. --}}
<div style="max-width:900px; margin:24px auto; padding:0 16px;">
    <div style="background:#fff; border:2px solid #f59e0b; border-radius:18px; box-shadow:0 6px 24px rgba(0,0,0,0.08); overflow:hidden;">
        <div style="background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; text-align:center; padding:22px 20px;">
            <div style="font-size:1.5rem; font-weight:800; line-height:1.3;">⚠️ Сервис больше не обслуживается</div>
        </div>
        <div style="padding:24px 22px; color:#334155; font-size:1rem; line-height:1.7;">
            <p style="margin:0 0 14px;">К сожалению, у нас больше нет времени обслуживать сервис, поэтому показ объявлений временно приостановлен. Модерация объявлений требует времени, и поддерживать её в нужном режиме мы сейчас не можем.</p>

            <p style="margin:0 0 14px;"><strong>Сервис продаётся.</strong> Вы можете купить его (целиком или долю — от 10%) и продолжить работу — мы поможем с передачей и сопровождением.</p>

            <div style="background:#fffbeb; border:1px solid #fcd34d; border-radius:12px; padding:14px 16px; margin:18px 0;">
                <strong style="color:#b45309;">💳 Возврат средств:</strong> если у вас оплачен статус и осталось <strong>больше 15 дней</strong> — напишите нам, вернём деньги.
            </div>

            <p style="margin:0 0 18px;">По вопросам <strong>покупки</strong> сервиса или <strong>возврата</strong> средств — пишите администратору:</p>

            <div style="text-align:center;">
                <a href="https://t.me/Sponsor_admin" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#0088cc,#0077b3); color:#fff; padding:13px 28px; border-radius:30px; text-decoration:none; font-size:1rem; font-weight:700; box-shadow:0 4px 14px rgba(0,136,204,0.35);">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:#fff;"><path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/></svg>
                    Написать @Sponsor_admin
                </a>
            </div>
        </div>
    </div>
</div>

@if(false)
@include('partials.city-filter')

<div class="massage-wrapper-v2">
    
    @if(isset($topPosts) && $topPosts->count() > 0)
        <div class="top-section-title">
            <span>⭐ Топ объявления</span>
        </div>

        @foreach($topPosts as $post)
            @php
                $contactAccess = 0;
                if ($currentUser) {
                    if ($currentUser->sex == 2) {
                        $contactAccess = ($post->sex == 1) ? 1 : 2;
                    } else {
                        if ($currentUser->prov == 1) {
                            $contactAccess = 4;
                        } elseif ($post->sex == 1) {
                            $contactAccess = 5;
                        } else {
                            $contactAccess = 3;
                        }
                    }
                }
                $postUser = DB::table('users')->where('email', $post->email)->first();
                $shortDescription = mb_strlen($post->discription) > 200
                    ? mb_substr($post->discription, 0, 200) . '...'
                    : $post->discription;
            @endphp

            <div class="card-v2 card-top">
                <div class="card-header-v2">
                    <div class="header-content-v2">

                        <!-- Фото профиля -->
                        <div class="photo-container">
                            @if($contactAccess == 0)
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a></span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @elseif($contactAccess == 1 || $contactAccess == 4)
                                @if(!empty($post->cover_img))
                                    <a href="{{ asset($post->cover_img->original_webp) }}" data-fancybox="top-{{ $post->id }}" data-caption="{{ $post->title }}">
                                        <img class="photo-v2 photo-clickable" src="{{ asset($post->cover_img->thumb_webp) }}" alt="Фото">
                                    </a>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @else
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото <a href="/become-verified">купите статус</a> проверенного пользователя</span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                        </div>

                        <div class="service-info-v2">
                            <div class="top-header-row">
                                <h3><a href="/post/detail/{{ $post->id }}">{{ $post->title }}</a></h3>
                                <span class="top-badge">ТОП</span>
                            </div>

                            @if($contactAccess == 0)
                                <div class="specialist-v2">
                                    <span style="color: red; font-size:12px;"><b>Для просмотра имени <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a></b></span>
                                </div>
                            @elseif(in_array($contactAccess, [1, 4, 5]))
                                <div class="specialist-v2">{{ $post->fio }}</div>
                            @else
                                <div class="specialist-v2">
                                    <span style="color: #78350f; font-size:12px;">Для просмотра имени <a href="/become-verified" style="color: #78350f;">купите статус</a> проверенного пользователя</span>
                                </div>
                            @endif

                            <div class="user-type-v2">
                                @if($post->sex == 1)
                                    Мужчина ищет Женщину
                                @else
                                    Женщина ищет Мужчину
                                @endif
                                • {{ $post->who == 1 ? 'Спонсор' : 'Содержанка' }}
                            </div>

                            <div class="location-date-v2">📍 Казахстан/{{ $post->city_name }} • {{ \Carbon\Carbon::parse($post->date)->format('d.m.Y') }}</div>

                            <div class="description-v2">{{ $shortDescription }}</div>
                        </div>

                        <!-- КОНТАКТЫ -->
                        <div class="contact-panel-v2">
                            @if(!empty($post->phone) || !empty($post->whats) || !empty($post->telegram))
                                @if($contactAccess == 0)
                                    <div class="restricted-message">
                                        Для просмотра контактов <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>
                                    </div>
                                @elseif($contactAccess == 1 || $contactAccess == 4)
                                    @if(!empty($post->phone))
                                        <a href="tel:{{ $post->phone }}" class="phone-v2">{{ $post->phone }}</a>
                                    @endif
                                    <div class="contact-buttons-v2">
                                        @if(!empty($post->whats))
                                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" class="contact-btn-v2 btn-whatsapp-v2" target="_blank">WhatsApp</a>
                                        @endif
                                        @if(!empty($post->telegram))
                                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" class="contact-btn-v2 btn-telegram-v2" target="_blank">{{ ltrim($post->telegram, '@') }}</a>
                                        @endif
                                    </div>
                                @else
                                    <div class="restricted-message restricted-sponsor">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                                        </svg>
                                        <span>Для просмотра <a href="/become-verified">купите статус проверенного пользователя</a></span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body-v2">
                    <div class="views-trusted-v2">
                        <div class="views-v2">👁 {{ $post->view }} просмотров</div>
                        @if(!empty($postUser) && $postUser->prov == 1)
                            <div class="trusted-sponsor-v2">✓ Проверенный пользователь</div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if(count($posts) == 0)
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px;">
            <h2 style="color: #64748b; margin-bottom: 15px;">Пока нет объявлений</h2>
            <p style="color: #94a3b8; margin-bottom: 25px;">Будьте первым, кто добавит объявление!</p>
            <a href="{{ route('post.create') }}" class="btn btn-success" style="padding: 12px 30px;">+ Добавить объявление</a>
        </div>
    @else
        
        @foreach($posts as $post)
            @php
                // НОВАЯ ЛОГИКА ДОСТУПА К КОНТАКТАМ
                // 0 - не авторизован
                // 1 - женщина смотрит мужское объявление - показываем контакты
                // 2 - женщина смотрит женское объявление - нужен статус
                // 3 - мужчина без статуса смотрит женское объявление - нужен статус
                // 4 - мужчина со статусом (prov=1) - показываем ВСЁ
                // 5 - мужчина без статуса смотрит мужское объявление - показываем имя

                $contactAccess = 0;

                if ($currentUser) {
                    if ($currentUser->sex == 2) {
                        // Женщина
                        if ($post->sex == 1) {
                            $contactAccess = 1; // мужское объявление - показываем
                        } else {
                            $contactAccess = 2; // женское - нужен статус
                        }
                    } else {
                        // Мужчина
                        if ($currentUser->prov == 1) {
                            $contactAccess = 4; // есть статус - показываем всё
                        } elseif ($post->sex == 1) {
                            $contactAccess = 5; // мужское объявление - показываем имя
                        } else {
                            $contactAccess = 3; // женское - нужен статус
                        }
                    }
                }
                
                // Получаем автора поста
                $postUser = DB::table('users')->where('email', $post->email)->first();
                
                // Обрезаем описание до 200 символов
                $shortDescription = mb_strlen($post->discription) > 200
                    ? mb_substr($post->discription, 0, 200) . '...'
                    : $post->discription;
            @endphp
            
            <div class="card-v2">
                <div class="card-header-v2">
                    <div class="header-content-v2">
                        
                        <!-- Фото профиля -->
                        <div class="photo-container">
                            @if($contactAccess == 0)
                                {{-- Не авторизован --}}
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a></span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @elseif($contactAccess == 1 || $contactAccess == 4)
                                {{-- Показываем фото --}}
                                @if(!empty($post->cover_img))
                                    <a href="{{ asset($post->cover_img->original_webp) }}" data-fancybox="post-{{ $post->id }}" data-caption="{{ $post->title }}">
                                        <img class="photo-v2 photo-clickable" src="{{ asset($post->cover_img->thumb_webp) }}" alt="Фото">
                                    </a>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @else
                                {{-- Нужен статус --}}
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото <a href="/become-verified">купите статус</a> проверенного спонсора</span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                        </div>
                        
                        <div class="service-info-v2">
                            <h3><a href="/post/detail/{{ $post->id }}">{{ $post->title }}</a></h3>
                            
                            <!-- Имя -->
                            @if($contactAccess == 0)
                                <div class="specialist-v2">
                                    <span style="color: red; font-size:12px;"><b>Для просмотра имени <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a></b></span>
                                </div>
                            @elseif(in_array($contactAccess, [1, 4, 5]))
                                <div class="specialist-v2">{{ $post->fio }}</div>
                            @else
                                <div class="specialist-v2">
                                    <span style="color: #92400e; font-size:12px;">Для просмотра имени <a href="/become-verified" style="color: #92400e;">купите статус</a> проверенного спонсора</span>
                                </div>
                            @endif
                            
                            <!-- Тип пользователя и пол -->
                            <div class="user-type-v2">
                                @if($post->sex == 1)
                                    Мужчина ищет Женщину
                                @else
                                    Женщина ищет Мужчину
                                @endif
                                • {{ $post->who == 1 ? 'Спонсор' : 'Содержанка' }}
                            </div>
                            
                            <div class="location-date-v2">📍 Казахстан/{{ $post->city_name }} • {{ \Carbon\Carbon::parse($post->date)->format('d.m.Y') }}</div>
                            
                            <div class="description-v2">
                                {{ $shortDescription }}
                            </div>
                        </div>
                        
                        <!-- КОНТАКТЫ -->
                        <div class="contact-panel-v2">
                            @if(!empty($post->phone) || !empty($post->whats) || !empty($post->telegram))
                                
                                @if($contactAccess == 0)
                                    {{-- Не авторизован --}}
                                    <div class="restricted-message">
                                        Для просмотра контактов <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>
                                    </div>
                                @elseif($contactAccess == 1 || $contactAccess == 4)
                                    {{-- Показываем контакты --}}
                                    @if(!empty($post->phone))
                                        <a href="tel:{{ $post->phone }}" class="phone-v2">{{ $post->phone }}</a>
                                    @endif
                                    <div class="contact-buttons-v2">
                                        @if(!empty($post->whats))
                                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" class="contact-btn-v2 btn-whatsapp-v2" target="_blank">WhatsApp</a>
                                        @endif
                                        @if(!empty($post->telegram))
                                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" class="contact-btn-v2 btn-telegram-v2" target="_blank">{{ ltrim($post->telegram, '@') }}</a>
                                        @endif
                                    </div>
                                @else
                                    {{-- Нужен статус --}}
                                    <div class="restricted-message restricted-sponsor">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                                        </svg>
                                        <span>Для просмотра <a href="/become-verified">купите статус проверенного пользователя</a></span>
                                    </div>
                                @endif
                                
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body-v2">
                    <div class="views-trusted-v2">
                        <div class="views-v2">👁 {{ $post->view }} просмотров</div>
                        @if(!empty($postUser) && $postUser->prov == 1)
                            <div class="trusted-sponsor-v2">
                                ✓ Проверенный пользователь
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        @endforeach
        
        <!-- Пагинация -->
        <div style="margin-top: 40px; display: flex; justify-content: center;">
            {{ $posts->links('pagination.custom') }}
        </div>
        
    @endif

</div>
@endif

<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>

@endsection