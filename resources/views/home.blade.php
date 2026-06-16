@extends('layouts.app')

@section('title', 'Спонсоры и Содержанки в Алматы, Астане, Казахстане.')

@section('meta_description', 'Используйте VPN для доступа к сайту. Вы можете найти для себя спонсора или содержанки в Алматы, Астане и других городах на данном портале.')

@section('content')

{{-- ТЕСТОВЫЙ ДИЗАЙН АНКЕТ (примерные данные). Старые объявления ниже спрятаны в @if(false). --}}
@php
// sex: 1 = мужчина, 2 = женщина; has_photo — есть ли загруженное фото
$testProfiles = [
    ['name' => 'Анна',     'age' => 24, 'city' => 'Алматы',    'sex' => 2, 'has_photo' => true],
    ['name' => 'Дарья',    'age' => 27, 'city' => 'Астана',    'sex' => 2, 'has_photo' => false],
    ['name' => 'Игорь',    'age' => 34, 'city' => 'Алматы',    'sex' => 1, 'has_photo' => false],
    ['name' => 'Карина',   'age' => 22, 'city' => 'Шымкент',   'sex' => 2, 'has_photo' => true],
    ['name' => 'Мария',    'age' => 25, 'city' => 'Алматы',    'sex' => 2, 'has_photo' => true],
    ['name' => 'Дмитрий',  'age' => 29, 'city' => 'Астана',    'sex' => 1, 'has_photo' => true],
    ['name' => 'Виктория', 'age' => 31, 'city' => 'Актобе',    'sex' => 2, 'has_photo' => false],
    ['name' => 'Софья',    'age' => 26, 'city' => 'Тараз',     'sex' => 2, 'has_photo' => false],
    ['name' => 'Андрей',   'age' => 38, 'city' => 'Караганда', 'sex' => 1, 'has_photo' => false],
    ['name' => 'Елена',    'age' => 28, 'city' => 'Павлодар',  'sex' => 2, 'has_photo' => true],
    ['name' => 'Айгерим',  'age' => 21, 'city' => 'Алматы',    'sex' => 2, 'has_photo' => false],
    ['name' => 'Камила',   'age' => 24, 'city' => 'Шымкент',   'sex' => 2, 'has_photo' => true],
];
$isRegistered = (bool) session('user_id');

// Фильтр «Кого ищу» (who: 1=мужчину, 2=девушку) применяем к тестовым анкетам по полу
$selectedWho = request()->get('who', 'all');
$visibleProfiles = $selectedWho === 'all'
    ? $testProfiles
    : array_values(array_filter($testProfiles, fn ($p) => (string) $p['sex'] === (string) $selectedWho));
@endphp

<style>
.profiles-wrap { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
.profiles-head { margin-bottom: 20px; }
.profiles-head h1 { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.profiles-head p { color: #64748b; font-size: 0.95rem; margin: 0; }
.profiles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 18px;
}
.profile-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    border: 1px solid #f1f5f9;
}
.profile-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.14); }
.profile-photo {
    position: relative;
    aspect-ratio: 3 / 4;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-photo .avatar-icon { width: 50%; height: 50%; fill: rgba(255,255,255,0.9); }
.lock-overlay { text-align: center; color: #fff; padding: 12px; }
.lock-overlay svg { width: 34px; height: 34px; fill: rgba(255,255,255,0.92); margin-bottom: 8px; }
.lock-overlay span { display: block; font-size: 0.8rem; line-height: 1.35; font-weight: 600; opacity: 0.96; }
.profile-info { padding: 12px 14px 14px; }
.profile-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.2;
}
.profile-city {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
    font-size: 0.88rem;
}
.profile-city svg { width: 14px; height: 14px; fill: #94a3b8; flex-shrink: 0; }
@media (max-width: 480px) {
    .profiles-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .profile-name { font-size: 0.95rem; }
}
</style>

<div class="profiles-wrap">
    <div class="profiles-head">
        <h1>Анкеты</h1>
        <p>Тестовый дизайн — примерные данные</p>
    </div>

    @include('partials.city-filter')

    <div class="profiles-grid">
        @forelse($visibleProfiles as $p)
        @php
            $locked = $p['has_photo'] && ! $isRegistered;
            if ($locked) {
                $bg = 'linear-gradient(135deg,#475569,#1e293b)';
            } elseif ($p['sex'] == 1) {
                $bg = 'linear-gradient(135deg,#4facfe,#0066ff)';
            } else {
                $bg = 'linear-gradient(135deg,#f6a5c0,#f5576c)';
            }
        @endphp
        <div class="profile-card">
            <div class="profile-photo {{ $locked ? 'locked' : '' }}" style="background: {{ $bg }};">
                @if($locked)
                    <div class="lock-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                        <span>Зарегистрируйтесь,<br>чтобы посмотреть фото</span>
                    </div>
                @elseif($p['sex'] == 1)
                    {{-- Аватар мужчины --}}
                    <svg class="avatar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g transform="translate(2.5,0)"><path d="M9.5,5.5A1.5,1.5 0 0,1 8,4A1.5,1.5 0 0,1 9.5,2.5A1.5,1.5 0 0,1 11,4A1.5,1.5 0 0,1 9.5,5.5M9.5,7C11.43,7 13,8.57 13,10.5V16H11V22H8V16H6V10.5C6,8.57 7.57,7 9.5,7Z"/></g></svg>
                @else
                    {{-- Аватар женщины --}}
                    <svg class="avatar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.94,8.31C13.62,7.52 12.85,7 12,7C11.15,7 10.39,7.52 10.06,8.31L7,16H9.5V22H14.5V16H17M12,2A2,2 0 0,1 14,4A2,2 0 0,1 12,6A2,2 0 0,1 10,4A2,2 0 0,1 12,2Z"/></svg>
                @endif
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ $p['name'] }}, {{ $p['age'] }}</div>
                <div class="profile-city">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                    {{ $p['city'] }}
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; text-align:center; color:#64748b; padding:40px 0;">Анкеты не найдены.</div>
        @endforelse
    </div>
</div>

@if(false)
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

<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>

@endif
@endsection