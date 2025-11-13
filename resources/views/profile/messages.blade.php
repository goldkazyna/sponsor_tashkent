@extends('layouts.app')

@section('title', 'Сообщения')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.messages-container {
    max-width: 1200px;
    margin: 0 auto;
}

.messages-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.messages-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.messages-header h1 svg {
    width: 28px;
    height: 28px;
    fill: #1a202c;
}

.back-to-cabinet {
    padding: 0.6rem 1.2rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.back-to-cabinet:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.back-to-cabinet svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.3s ease;
}

.filter-tab:hover {
    border-color: #cbd5e1;
    color: #1a202c;
}

.filter-tab.active {
    background: #1a202c;
    border-color: #1a202c;
    color: white;
}

.conversations-list {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.conversation-item {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 1rem;
    align-items: start;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.conversation-item:hover {
    background: #f8fafc;
}

.conversation-item:last-child {
    border-bottom: none;
}

.conversation-item.unread {
    background: #f0f9ff;
}

.conversation-item.unread:hover {
    background: #e0f2fe;
}

.conversation-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.conversation-avatar svg {
    width: 28px;
    height: 28px;
    fill: #94a3b8;
}

.conversation-avatar.male {
    background: #dbeafe;
}

.conversation-avatar.male svg {
    fill: #1e40af;
}

.conversation-avatar.female {
    background: #fce7f3;
}

.conversation-avatar.female svg {
    fill: #be185d;
}

.conversation-content {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.5rem;
}

.conversation-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a202c;
}

.conversation-time {
    font-size: 0.8rem;
    color: #94a3b8;
    white-space: nowrap;
}

.conversation-message {
    color: #64748b;
    font-size: 0.95rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 0.5rem;
}

.conversation-item.unread .conversation-message {
    color: #1a202c;
    font-weight: 600;
}

.conversation-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.conversation-post {
    font-size: 0.8rem;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.conversation-post svg {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

.unread-badge {
    background: #ef4444;
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-icon svg {
    width: 40px;
    height: 40px;
    fill: #94a3b8;
}

.empty-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: #64748b;
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .messages-header {
        flex-direction: column;
        gap: 1rem;
        padding: 1.2rem;
    }

    .messages-header h1 {
        font-size: 1.4rem;
        width: 100%;
    }

    .back-to-cabinet {
        width: 100%;
        justify-content: center;
    }

    .filter-tabs {
        width: 100%;
        justify-content: center;
    }

    .conversation-item {
        padding: 1rem;
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }

    .conversation-name {
        font-size: 1rem;
    }

    .conversation-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<div class="messages-container">
    
    <!-- Шапка -->
    <div class="messages-header">
        <h1>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
            </svg>
            Сообщения
        </h1>
        
        <a href="{{ route('profile.posts') }}" class="back-to-cabinet">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
            В личный кабинет
        </a>
    </div>

    <div class="filter-tabs" style="margin-bottom: 1.5rem;">
        <button class="filter-tab active">Все (8)</button>
        <button class="filter-tab">Непрочитанные (3)</button>
    </div>

    <!-- Список диалогов -->
    <div class="conversations-list">
        
        <!-- Диалог 1: Непрочитанное сообщение от женщины -->
        <a href="{{ route('profile.messages.chat', 1) }}" class="conversation-item unread">
            <div class="conversation-avatar female">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Анна Петрова</div>
                    <div class="conversation-time">5 мин назад</div>
                </div>
                <div class="conversation-message">
                    Привет! Я видела ваше объявление. Можем обсудить детали?
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Ищу спонсора в Ташкенте
                    </div>
                    <div class="unread-badge">2 новых</div>
                </div>
            </div>
        </a>

        <!-- Диалог 2: Непрочитанное от мужчины -->
        <a href="{{ route('profile.messages.chat', 2) }}" class="conversation-item unread">
            <div class="conversation-avatar male">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Дмитрий Иванов</div>
                    <div class="conversation-time">1 час назад</div>
                </div>
                <div class="conversation-message">
                    Здравствуйте! Интересует серьёзное знакомство. Вы свободны сегодня вечером?
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Ищу девушку для встреч
                    </div>
                    <div class="unread-badge">1 новое</div>
                </div>
            </div>
        </a>

        <!-- Диалог 3 -->
        <a href="{{ route('profile.messages.chat', 3) }}" class="conversation-item">
            <div class="conversation-avatar female">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Елена Смирнова</div>
                    <div class="conversation-time">3 часа назад</div>
                </div>
                <div class="conversation-message">
                    Спасибо за быстрый ответ! Буду рада познакомиться.
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Спонсор, 35 лет, Ташкент
                    </div>
                </div>
            </div>
        </a>

        <!-- Диалог 4 -->
        <a href="{{ route('profile.messages.chat', 4) }}" class="conversation-item">
            <div class="conversation-avatar male">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Алексей Козлов</div>
                    <div class="conversation-time">Вчера в 18:30</div>
                </div>
                <div class="conversation-message">
                    Договорились. Встречаемся завтра в 19:00.
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Ищу девушку для отношений
                    </div>
                </div>
            </div>
        </a>

        <!-- Диалог 5 -->
        <a href="{{ route('profile.messages.chat', 5) }}" class="conversation-item unread">
            <div class="conversation-avatar female">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Мария Волкова</div>
                    <div class="conversation-time">2 дня назад</div>
                </div>
                <div class="conversation-message">
                    Добрый день! Давайте обсудим условия сотрудничества.
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Ищу спонсора для путешествий
                    </div>
                    <div class="unread-badge">1 новое</div>
                </div>
            </div>
        </a>

        <!-- Диалог 6 -->
        <a href="{{ route('profile.messages.chat', 6) }}" class="conversation-item">
            <div class="conversation-avatar male">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Сергей Новиков</div>
                    <div class="conversation-time">3 дня назад</div>
                </div>
                <div class="conversation-message">
                    Хорошо, подумаю над вашим предложением.
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Проверенный спонсор, бизнесмен
                    </div>
                </div>
            </div>
        </a>

        <!-- Диалог 7 -->
        <a href="{{ route('profile.messages.chat', 7) }}" class="conversation-item">
            <div class="conversation-avatar female">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Ольга Соколова</div>
                    <div class="conversation-time">7 дней назад</div>
                </div>
                <div class="conversation-message">
                    Благодарю за приятное общение!
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Ищу серьёзные отношения
                    </div>
                </div>
            </div>
        </a>

        <!-- Диалог 8 -->
        <a href="{{ route('profile.messages.chat', 8) }}" class="conversation-item">
            <div class="conversation-avatar male">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-header">
                    <div class="conversation-name">Игорь Морозов</div>
                    <div class="conversation-time">14 окт</div>
                </div>
                <div class="conversation-message">
                    К сожалению, сейчас не готов к новым знакомствам.
                </div>
                <div class="conversation-meta">
                    <div class="conversation-post">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                        </svg>
                        Спонсор в поиске
                    </div>
                </div>
            </div>
        </a>

    </div>

</div>

<script>
// Фильтрация диалогов
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.textContent.toLowerCase();
        console.log('Фильтр:', filter);
    });
});
</script>

@endsection