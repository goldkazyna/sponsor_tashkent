@extends('layouts.app')

@section('title', $post->title . ' | Спонсоры и содержанки в Алматы, Астане, Казахстане.')

@section('content')

<!-- Fancybox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<style>
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.card-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 3rem;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.sidebar {
    background: #f8fafc;
    padding: 2rem;
}

.main-photo {
    width: 100%;
    height: 280px;
    background: #333;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    object-fit: cover;
    display: block;
}

.photo-thumbnails {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.thumbnail {
    width: 70px;
    height: 70px;
    background: #333;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    opacity: 0.7;
    object-fit: cover;
}

.thumbnail:hover,
.thumbnail.active {
    opacity: 1;
    border-color: #4a9b8e;
    transform: translateY(-2px);
}

.master-info {
    text-align: center;
    margin-bottom: 2rem;
}

.master-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.master-location {
    color: #64748b;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.phone-section {
    margin-bottom: 1.5rem;
}

.contact-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-btn {
    padding: 0.875rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.phone-btn {
    background: #1a202c;
    color: white;
}

.phone-btn:hover {
    background: #2d3748;
    transform: translateY(-1px);
    text-decoration: none;
    color: white;
}

.whatsapp-btn {
    background: #25D366;
    color: white;
}

.whatsapp-btn:hover {
    background: #128C7E;
    transform: translateY(-1px);
    text-decoration: none;
    color: white;
}

.telegram-btn {
    background: #0088cc;
    color: white;
}

.telegram-btn:hover {
    background: #006699;
    transform: translateY(-1px);
    text-decoration: none;
    color: white;
}

.stats-section {
    text-align: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stats-number {
    font-size: 1.2rem;
    font-weight: bold;
    color: #4a9b8e;
}

.stats-label {
    font-size: 0.8rem;
    color: #64748b;
}

.main-content {
    padding: 2.5rem;
}

.content-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.listing-date {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.listing-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #1a202c;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.description-section {
    margin-bottom: 3rem;
}

.description-text {
    color: #4a5568;
    font-size: 1.05rem;
    line-height: 1.7;
    white-space: pre-wrap;
}

.description-text p {
    margin-bottom: 1rem;
}

/* Красный блок - не авторизован */
.restricted-box {
    background: #fef2f2;
    border: 2px solid #fecaca;
    color: #dc2626;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    font-size: 0.95rem;
    line-height: 1.6;
}

.restricted-box a {
    color: #dc2626;
    text-decoration: underline;
    font-weight: 600;
}

/* Жёлтый блок - нужен статус */
.restricted-sponsor {
    background: #fef3c7;
    border: 2px solid #fcd34d;
    color: #92400e;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    font-size: 0.95rem;
    line-height: 1.6;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.restricted-sponsor svg {
    width: 24px;
    height: 24px;
    fill: #f59e0b;
    flex-shrink: 0;
}

.restricted-sponsor a {
    color: #92400e;
    text-decoration: underline;
    font-weight: 600;
}

.back-button {
    display: inline-block;
    margin-bottom: 20px;
    color: #64748b;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.back-button:hover {
    color: #0f172a;
    text-decoration: none;
}

/* Кнопка написать сообщение */
.btn-message {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem 1.5rem;
    background: #1a202c;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.btn-message:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26, 32, 44, 0.3);
    color: white;
    text-decoration: none;
}

.btn-message-disabled {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem 1.5rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    margin-top: 1rem;
    border: 2px solid #fcd34d;
}

.btn-message-disabled:hover {
    color: #92400e;
    text-decoration: none;
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .card-layout {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .sidebar {
        order: 1;
        padding: 1.5rem;
        background: #f8fafc;
    }
    
    .main-content {
        order: 2;
        padding: 1.5rem;
    }
    
    .main-photo {
        width: 100%;
        height: auto !important;
        margin-bottom: 1rem;
    }
    
    .thumbnail {
        height: 60px;
        width: 60px;
    }
    
    .master-name {
        font-size: 1.3rem;
        margin-bottom: 0.3rem;
    }
    
    .master-location {
        font-size: 0.85rem;
    }
    
    .contact-btn {
        padding: 0.75rem;
        font-size: 0.85rem;
    }
    
    .stats-number {
        font-size: 1rem;
    }
    
    .stats-label {
        font-size: 0.75rem;
    }
    
    .listing-title {
        font-size: 1.8rem;
        margin-bottom: 0.8rem;
    }
    
    .listing-date {
        font-size: 0.8rem;
    }
    
    .description-text {
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .btn-message,
    .btn-message-disabled {
        width: 100%;
    }
}
</style>

<div class="container">
    <a href="/" class="back-button">← Вернуться к объявлениям</a>
    
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
        
        // Получаем владельца объявления по email
        $postOwner = DB::table('users')->where('email', $post->email)->first();
    @endphp
    
    <div class="card-layout">
        <!-- Боковая панель -->
        <div class="sidebar">
            
            <!-- ФОТО -->
            @if($contactAccess == 0)
                {{-- Не авторизован --}}
                @if(count($photos) > 0)
                    <div class="restricted-box" style="margin-bottom: 1.5rem;">
                        Для просмотра фото <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>
                    </div>
                @else
                    <img src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото" class="main-photo">
                @endif
            @elseif($contactAccess == 1 || $contactAccess == 4)
                {{-- Показываем фото --}}
                @if(count($photos) > 0)
                    <a href="{{ asset($photos[0]->original_webp) }}" data-fancybox="gallery" data-caption="{{ $post->title }}">
                        <img src="{{ asset($photos[0]->thumb_webp) }}" alt="{{ $post->fio }}" class="main-photo">
                    </a>
                    @if(count($photos) > 1)
                        <div class="photo-thumbnails">
                            @foreach($photos as $photo)
                                <a href="{{ asset($photo->original_webp) }}" data-fancybox="gallery" data-caption="{{ $post->title }}">
                                    <img src="{{ asset($photo->thumb_webp) }}" alt="" class="thumbnail">
                                </a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <img src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото" class="main-photo">
                @endif
            @else
                {{-- Нужен статус --}}
                @if(count($photos) > 0)
                    <div class="restricted-sponsor" style="margin-bottom: 1.5rem; flex-direction: column;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                        </svg>
                        <span>Для просмотра фото <a href="/become-verified">купите статус</a> проверенного спонсора</span>
                    </div>
                @else
                    <img src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото" class="main-photo">
                @endif
            @endif
            
            <!-- ИМЯ И ЛОКАЦИЯ -->
            <div class="master-info">
                <div class="master-name">
                    @if($contactAccess == 0)
                        <span style="color: #dc2626; font-size: 0.9rem;">Для просмотра имени <a href="{{ route('login') }}" style="text-decoration: underline;">авторизуйтесь</a></span>
                    @elseif(in_array($contactAccess, [1, 4, 5]))
                        {{ $post->fio }}
                    @else
                        <span style="color: #92400e; font-size: 0.9rem;">Для просмотра имени <a href="/become-verified" style="color: #92400e;">купите статус</a> проверенного спонсора</span>
                    @endif
                </div>
                <div class="master-location">
                    <span>📍</span>
                    <span>Казахстан - {{ $post->city_name }}</span>
                </div>
            </div>

            <!-- КОНТАКТЫ -->
            <div class="phone-section">
                @if($contactAccess == 0)
                    {{-- Не авторизован --}}
                    <div class="restricted-box">
                        Для просмотра контактов <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>
                    </div>
                @elseif($contactAccess == 1 || $contactAccess == 4)
                    {{-- Показываем контакты --}}
                    <div class="contact-buttons">
                        @if(!empty($post->phone))
                            <a href="tel:{{ $post->phone }}" class="contact-btn phone-btn">
                                📞 {{ $post->phone }}
                            </a>
                        @endif
                        
                        @if(!empty($post->whats))
                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" class="contact-btn whatsapp-btn" target="_blank">
                                WhatsApp
                            </a>
                        @endif
                        
                        @if(!empty($post->telegram))
                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" class="contact-btn telegram-btn" target="_blank">
                                Telegram
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Нужен статус --}}
                    <div class="restricted-sponsor">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                        </svg>
                        <span>Для просмотра <a href="/become-verified">купите статус</a> проверенного спонсора</span>
                    </div>
                @endif
            </div>

            <div class="stats-section">
                <div class="stats-number">👁 {{ $post->view }}</div>
                <div class="stats-label">просмотров</div>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="main-content">
            <div class="content-header">
                <div class="listing-date">{{ \Carbon\Carbon::parse($post->date)->format('d.m.Y H:i') }}</div>
                <h1 class="listing-title">{{ $post->title }}</h1>
                
                <!-- Мета информация -->
                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 1rem; color: #64748b; font-size: 0.9rem;">
                    <div>
                        <span>{{ $post->sex == 1 ? '👨 Мужчина ищет Женщину' : '👩 Женщина ищет Мужчину' }}</span>
                    </div>
                    <div>
                        <span>💼 {{ $post->who == 1 ? 'Спонсор' : 'Содержанка' }}</span>
                    </div>
                    @if(!empty($postUser) && $postUser->prov == 1)
                        <div style="color: #059669;">
                            <span>✓ Проверенный пользователь</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="description-section">
                <h2 style="font-size: 1.4rem; font-weight: 700; color: #1a202c; margin-bottom: 1rem;">Описание</h2>
                <div class="description-text">{{ $post->discription }}</div>
            </div>
            
            <!-- КНОПКА НАПИСАТЬ СООБЩЕНИЕ -->
            @if($contactAccess == 0)
                {{-- Не авторизован --}}
                <a href="{{ route('login') }}" class="btn-message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; margin-right: 8px;">
                        <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
                    </svg>
                    Написать сообщение
                </a>
            @elseif($contactAccess == 1 || $contactAccess == 4)
                {{-- Показываем кнопку (женщина смотрит мужское или проверенный пользователь) --}}
                @if($postOwner && $currentUser && $currentUser->id != $postOwner->id)
                    <a href="{{ route('profile.messages.chat', $postOwner->id) }}" class="btn-message">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; margin-right: 8px;">
                            <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
                        </svg>
                        Написать сообщение
                    </a>
                @elseif(!$postOwner)
                    {{-- Владелец не найден - показываем кнопку всё равно, но без ссылки --}}
                    <span class="btn-message" style="opacity: 0.5; cursor: not-allowed;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; margin-right: 8px;">
                            <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
                        </svg>
                        Сообщения недоступны
                    </span>
                @endif
            @else
                {{-- Нужен статус --}}
                <a href="/become-verified" class="btn-message-disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: #f59e0b; margin-right: 8px;">
                        <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                    </svg>
                    Для сообщений купите статус проверенного пользователя
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>

@endsection