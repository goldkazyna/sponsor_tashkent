@extends('layouts.app')

@section('title', 'Главная - Спонсоры Ташкент')

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
</style>
@include('partials.city-filter')
<div class="massage-wrapper-v2">
    
    @if(count($posts) == 0)
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px;">
            <h2 style="color: #64748b; margin-bottom: 15px;">Пока нет объявлений</h2>
            <p style="color: #94a3b8; margin-bottom: 25px;">Будьте первым, кто добавит объявление!</p>
            <a href="{{ route('post.create') }}" class="btn btn-success" style="padding: 12px 30px;">+ Добавить объявление</a>
        </div>
    @else
        
        @foreach($posts as $post)
            @php
                // Определяем уровень доступа (как в CodeIgniter)
                // 0 - гость
                // 1 - авторизован, пол = мужчина (спонсор)
                // 2 - авторизован, пол = женщина (содержанка) - ограниченный доступ
                // 3 - авторизован со статусом проверенного спонсора
                
                $access = 0;
                
                if ($currentUser) {
                    if ($currentUser->sex == 1) {
                        // Мужчина - спонсор
                        $access = 1;
                    } else {
                        // Женщина - содержанка, нужен статус проверенного спонсора
                        $access = 2;
                    }
                    
                    // Если есть статус проверенного спонсора
                    if ($currentUser->prov == 1) {
                        $access = 3;
                    }
                }
                
                // Получаем автора поста
                $postUser = DB::table('users')->where('email', $post->email)->first();
                
                // Обрезаем описание до 200 символов
                $shortDescription = mb_strlen($post->description) > 200 
                    ? mb_substr($post->description, 0, 200) . '...' 
                    : $post->description;
            @endphp
            
            <div class="card-v2">
                <div class="card-header-v2">
                    <div class="header-content-v2">
                        
                        <!-- Фото профиля с логикой доступа -->
                        <div class="photo-container">
                            @if($access == 0)
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a> на сайте</span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                            
                            @if($access == 1 && $currentUser->sex == 1)
                                @if(!empty($post->cover_img))
                                    <a href="{{ asset($post->cover_img->original_webp) }}" data-fancybox="post-{{ $post->id }}" data-caption="{{ $post->title }}">
                                        <img class="photo-v2 photo-clickable" src="{{ asset($post->cover_img->thumb_webp) }}" alt="Фото">
                                    </a>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                            
                            @if($access == 1 && $currentUser->sex == 2)
                                <div class="photo-placeholder-v2">
                                    <span>Для просмотра фото необходимо купить статус проверенного спонсора</span>
                                </div>
                            @endif
                            
                            @if($access == 2)
                                @if(!empty($post->cover_img))
                                    <div class="photo-placeholder-v2">
                                        <span>Для просмотра фото необходимо купить статус проверенного спонсора</span>
                                    </div>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                            
                            @if($access == 3)
                                @if(!empty($post->cover_img))
                                    <a href="{{ asset($post->cover_img->original_webp) }}" data-fancybox="post-{{ $post->id }}" data-caption="{{ $post->title }}">
                                        <img class="photo-v2 photo-clickable" src="{{ asset($post->cover_img->thumb_webp) }}" alt="Фото">
                                    </a>
                                @else
                                    <img class="photo-v2" src="{{ asset('images/' . ($post->sex == 1 ? 'mens' : 'girls') . '.png') }}" alt="Фото">
                                @endif
                            @endif
                        </div>
                        
                        <div class="service-info-v2">
                            <h3><a href="/posts/{{ $post->slug }}">{{ $post->title }}</a></h3>
                            
                            <!-- Имя с логикой доступа -->
                            @if($access == 0)
                                <div class="specialist-v2">
                                    <span style="color: red; font-size:12px;"><b>Для просмотра имени <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a> на сайте</b></span>
                                </div>
                            @endif
                            
                            @if($access == 1 || $access == 3)
                                <div class="specialist-v2">{{ $post->fio }}</div>
                            @endif
                            
                            @if($access == 2)
                                <div class="specialist-v2">
                                    <span style="color: red; font-size:12px;">Для просмотра имени необходимо купить статус проверенного спонсора</span>
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
                            
                            <div class="location-date-v2">📍 Узбекистан/{{ $post->city }} • {{ \Carbon\Carbon::parse($post->date)->format('d.m.Y') }}</div>
                            
                            <div class="description-v2">
                                {{ $shortDescription }}
                            </div>
                        </div>
                        
                        <div class="contact-panel-v2">
                            
                            <!-- Контакты с логикой доступа -->
                            @if(!empty($post->phone) || !empty($post->whats) || !empty($post->telegram))
                                
                                @if($access == 0)
                                    <div class="restricted-message">
                                        Для просмотра контактов <a href="{{ route('login') }}">авторизуйтесь</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a> на сайте
                                    </div>
                                @endif
                                
                                @if($access == 1 && $currentUser->sex == 1)
                                    @if(!empty($post->phone))
                                        <a href="tel:{{ $post->phone }}" class="phone-v2">
                                            {{ $post->phone }}
                                        </a>
                                    @endif
                                    
                                    <div class="contact-buttons-v2">
                                        @if(!empty($post->whats))
                                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" 
                                               class="contact-btn-v2 btn-whatsapp-v2" 
                                               target="_blank">
                                                WhatsApp
                                            </a>
                                        @endif
                                        
                                        @if(!empty($post->telegram))
                                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" 
                                               class="contact-btn-v2 btn-telegram-v2"
                                               target="_blank">
                                                {{ ltrim($post->telegram, '@') }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($access == 1 && $currentUser->sex == 2)
                                    <div class="restricted-message">
                                        Для просмотра контактов необходимо купить статус проверенного спонсора
                                    </div>
                                @endif
                                
                                @if($access == 2)
                                    <div class="restricted-message">
                                        Для просмотра контактов необходимо купить статус проверенного спонсора
                                    </div>
                                @endif
                                
                                @if($access == 3)
                                    @if(!empty($post->phone))
                                        <a href="tel:{{ $post->phone }}" class="phone-v2">
                                            {{ $post->phone }}
                                        </a>
                                    @endif
                                    
                                    <div class="contact-buttons-v2">
                                        @if(!empty($post->whats))
                                            <a href="https://api.whatsapp.com/send?phone={{ $post->whats }}" 
                                               class="contact-btn-v2 btn-whatsapp-v2"
                                               target="_blank">
                                                WhatsApp
                                            </a>
                                        @endif
                                        
                                        @if(!empty($post->telegram))
                                            <a href="https://t.me/{{ ltrim($post->telegram, '@') }}" 
                                               class="contact-btn-v2 btn-telegram-v2"
                                               target="_blank">
                                                {{ ltrim($post->telegram, '@') }}
                                            </a>
                                        @endif
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
                                ✓ Проверенный спонсор
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
    // Инициализация Fancybox
    Fancybox.bind("[data-fancybox]", {
        // Настройки
    });
</script>

<style>
    /* Курсор pointer для кликабельных фото */
    .photo-clickable {
        cursor: pointer;
    }
</style>

@endsection