@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<div class="cabinet-container">
    
    <!-- Шапка -->
    <div class="welcome-section">
        <div class="welcome-info">
            <div class="welcome-avatar">
                👤
            </div>
            <div class="welcome-text">
                <h1>{{ $user->fio ?? $user->email }}</h1>
                <p>{{ $user->email }} • Последний вход: {{ now()->format('d.m.Y') }}</p>
            </div>
        </div>
        <div class="welcome-actions">
            <button class="action-btn cabinet-btn-primary" onclick="window.location.href='{{ route('profile.anketa') }}'">📝 Моя анкета</button>
            <button class="action-btn cabinet-btn-secondary" onclick="window.location.href='{{ route('profile.settings') }}'">⚙️ Настройки</button>
            <button class="action-btn" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;" onclick="if(confirm('Вы уверены, что хотите удалить аккаунт? Это действие необратимо.')) { fetch('{{ route('profile.delete') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.href = '/') }">🗑 Удалить аккаунт</button>
        </div>
    </div>
    
    <!-- Карточки меню -->
    <div class="menu-cards">
        
        <a href="{{ route('profile.anketa') }}" class="menu-card {{ $activeSection == 'anketa' ? 'active' : '' }}">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="card-content">
                <div class="card-title">Моя анкета</div>
                <span class="card-badge">Заполнить</span>
            </div>
        </a>

		<a href="{{ route('profile.messages') }}" class="menu-card {{ $activeSection == 'messages' ? 'active' : '' }}">
			<div class="card-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
					<path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
				</svg>
			</div>
			<div class="card-content">
				<div class="card-title">Сообщения</div>
				@php
					$unreadCount = DB::table('messages')
						->where('receiver_id', $user->id)
						->where('is_read', 0)
						->count();
				@endphp
				@if($unreadCount > 0)
					<span class="card-badge new">{{ $unreadCount }} новых</span>
				@else
					<span class="card-badge">Нет новых</span>
				@endif
			</div>
		</a>
        
        <a href="{{ route('profile.settings') }}" class="menu-card {{ $activeSection == 'settings' ? 'active' : '' }}">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                </svg>
            </div>
            <div class="card-content">
                <div class="card-title">Настройки профиля</div>
                <span class="card-badge">Обновить</span>
            </div>
        </a>
        
    </div>

    <!-- Рабочая область -->
    <div class="work-area">
        <div class="work-area-header">
            <h2 class="work-area-title">{{ $sectionTitle }}</h2>
        </div>
        
        <div class="work-area-body">
            @if(true)
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                        </svg>
                    </div>
                    <div class="empty-title">Добро пожаловать!</div>
                    <div class="empty-description">
                        Заполните свою анкету, чтобы она появилась на сайте знакомств.
                    </div>
                    <a href="{{ route('profile.anketa') }}" class="empty-action">📝 Заполнить анкету</a>
                </div>
            @elseif(false)
                @if(isset($posts) && count($posts) > 0)
                    <!-- Список объявлений -->
                    <div class="posts-list">
                        @foreach($posts as $post)
                        <div class="post-card">
                            <div class="post-card-body">
                                <!-- Миниатюра -->
                                <div class="post-thumbnail">
                                    @if(isset($post->cover_img) && !empty($post->cover_img->thumb_webp))
                                        <img src="{{ asset('/' . $post->cover_img->thumb_webp) }}" alt="{{ $post->title }}">
                                    @else
                                        <div class="post-thumbnail-empty">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 48px; height: 48px; fill: #94a3b8;">
                                                <path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Информация -->
                                <div class="post-info">
                                    <a href="{{ route('post.detail', $post->id) }}" class="post-title" target="_blank">
                                        {{ $post->title }}
                                    </a>
                                    
                                    <div class="post-meta">
                                        <span class="post-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;">
                                                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($post->date)->format('d.m.Y') }}
                                        </span>
                                        <span class="post-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;">
                                                <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                            </svg>
                                            {{ $post->view }} просмотров
                                        </span>
                                        <span class="post-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;">
                                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                            </svg>
                                            {{ $post->city_name }}
                                        </span>
                                    </div>

                                    @if(!empty($post->discription))
                                    <div class="post-description">
                                        {{ $post->discription }}
                                    </div>
                                    @endif

                                    <div class="post-status {{ $post->del == 0 ? 'active' : 'inactive' }}">
                                        @if($post->del == 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;">
                                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                                            </svg>
                                            Опубликовано
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor;">
                                                <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                                            </svg>
                                            Удалено
                                        @endif
                                    </div>
                                </div>

                                <!-- Действия -->
                                <div class="post-actions">
                                    <a href="{{ route('profile.post.edit', $post->id) }}" class="post-btn post-btn-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"/>
                                        </svg>
                                        Редактировать
                                    </a>
                                    @if($post->is_top)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: #059669; font-size: 0.8rem; font-weight: 600;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: #059669;">
                                            <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                                        </svg>
                                        В ТОПе
                                    </span>
                                    @else
                                    <a href="/boost-top?post_id={{ $post->id }}" class="post-btn post-btn-top">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                        </svg>
                                        Поднять в ТОП
                                    </a>
                                    @endif
                                    <button class="post-btn post-btn-delete" onclick="deletePost({{ $post->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                        </svg>
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Пустое состояние (когда нет объявлений) -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M15,18V16H8V18H15M18,14V12H8V14H18Z"/>
                            </svg>
                        </div>
                        <div class="empty-title">У вас пока нет объявлений</div>
                        <div class="empty-description">
                            Создайте своё первое объявление, чтобы начать поиск
                        </div>
                        <a href="{{ route('post.create') }}" class="empty-action">+ Создать объявление</a>
                    </div>
                @endif
            @else
                <!-- Заглушка для других разделов -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M15,18V16H8V18H15M18,14V12H8V14H18Z"/>
                        </svg>
                    </div>
                    <div class="empty-title">Раздел в разработке</div>
                    <div class="empty-description">
                        Этот раздел скоро будет доступен
                    </div>
                </div>
            @endif
        </div>
    </div>
    
</div>

<script>
// Функция удаления объявления
function deletePost(postId) {
    if (!confirm('Вы уверены, что хотите удалить это объявление?')) {
        return;
    }

    // Отправляем AJAX запрос
    fetch('/profile/post/delete/' + postId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Объявление успешно удалено');
            location.reload();
        } else {
            alert('Ошибка при удалении: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Ошибка при удалении объявления');
    });
}
// Автообновление счётчика с паузой на неактивной вкладке
let counterInterval = null;

function updateUnreadCount() {
    fetch('{{ route("profile.messages.unread") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.menu-card[href="{{ route("profile.messages") }}"] .card-badge');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count + ' новых';
                        badge.classList.add('new');
                    } else {
                        badge.textContent = 'Нет новых';
                        badge.classList.remove('new');
                    }
                }
            }
        })
        .catch(error => console.error('Ошибка обновления счётчика:', error));
}

// 🔥 УПРАВЛЕНИЕ СЧЁТЧИКОМ ПРИ СМЕНЕ ВКЛАДКИ
function startCounter() {
    if (!counterInterval) {
        counterInterval = setInterval(updateUnreadCount, 10000);
        console.log('✅ Обновление счётчика запущено');
    }
}

function stopCounter() {
    if (counterInterval) {
        clearInterval(counterInterval);
        counterInterval = null;
        console.log('⏸️ Обновление счётчика остановлено');
    }
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopCounter(); // Вкладка неактивна - СТОП
    } else {
        updateUnreadCount(); // Сразу обновляем
        startCounter(); // Возобновляем
    }
});

// Запускаем при загрузке
startCounter();
</script>
@endsection
