@extends('layouts.app')

@section('title', $post->title . ' - Спонсоры Ташкент')

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

.restricted-box {
    background: #fef2f2;
    border: 2px solid #fecaca;
    color: #dc2626;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    font-size: 1rem;
    line-height: 1.6;
}

.restricted-box a {
    color: #dc2626;
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
}
</style>

<div class="container">
    <a href="/" class="back-button">← Вернуться к объявлениям</a>
    
    @php
        // Определяем уровень доступа
        $access = 0;
        
        if ($currentUser) {
            if ($currentUser->sex == 1) {
                $access = 1;
            } else {
                $access = 2;
            }
            
            if ($currentUser->prov == 1) {
                $access = 3;
            }
        }
    @endphp
    
    <div class="card-layout">
        <!-- Боковая панель -->
        <div class="sidebar">
            <!-- Главное фото -->
            @if(count($photos) > 0 && ($access == 1 || $access == 3))
                <a href="{{ asset($photos[0]->original_webp) }}" data-fancybox="gallery" data-caption="{{ $post->title }}">
                    <img src="{{ asset($photos[0]->thumb_webp) }}" alt="{{ $post->fio }}" class="main-photo">
                </a>
                
                <!-- Миниатюры -->
                @if(count($photos) > 1)
                    <div class="photo-thumbnails">
                        @foreach($photos as $photo)
                            <a href="{{ asset($photo->original_webp) }}" data-fancybox="gallery" data-caption="{{ $post->title }}">
                                <img src="{{ asset($photo->thumb_webp) }}" alt="" class="thumbnail">
                            </a>
                        @endforeach
                    </div>
                @endif
            @elseif($access == 0 || $access == 2)
                <div class="restricted-box" style="margin-bottom: 1.5rem;">
                    @if($access == 0)
                        Для просмотра фото <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a> на сайте
                    @else
                        Для просмотра фото необходимо купить статус проверенного спонсора
                    @endif
                </div>
            @else
                <img src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото" class="main-photo">
            @endif
            
            <div class="master-info">
                <div class="master-name">
                    @if($access == 0)
                        <span style="color: #dc2626; font-size: 0.9rem;">Для просмотра имени <a href="{{ route('login') }}" style="text-decoration: underline;">авторизуйтесь</a></span>
                    @elseif($access == 2)
                        <span style="color: #dc2626; font-size: 0.9rem;">Для просмотра имени купите статус проверенного спонсора</span>
                    @else
                        {{ $post->fio }}
                    @endif
                </div>
                <div class="master-location">
                    <span>📍</span>
                    <span>Узбекистан - {{ $post->city }}</span>
                </div>
            </div>

            <!-- Контакты -->
            <div class="phone-section">
                @if($access == 0)
                    <div class="restricted-box">
                        Для просмотра контактов <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a> на сайте
                    </div>
                @elseif($access == 2)
                    <div class="restricted-box">
                        Для просмотра контактов необходимо купить статус проверенного спонсора
                    </div>
                @elseif($access == 1 || $access == 3)
                    <div class="contact-buttons">
                        @if(!empty($post->phone))
                            <a href="tel:{{ $post->phone }}" class="contact-btn phone-btn">
                                📞 {{ $post->phone }}
                            </a>
                        @endif
                        
                        @if(!empty($post->whats))
                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" 
                               class="contact-btn whatsapp-btn" 
                               target="_blank">
                                WhatsApp
                            </a>
                        @endif
                        
                        @if(!empty($post->telegram))
                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" 
                               class="contact-btn telegram-btn"
                               target="_blank">
                                Telegram
                            </a>
                        @endif
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
                            <span>✓ Проверенный спонсор</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="description-section">
                <h2 style="font-size: 1.4rem; font-weight: 700; color: #1a202c; margin-bottom: 1rem;">Описание</h2>
                <div class="description-text">{{ $post->description }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Fancybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>

@endsection