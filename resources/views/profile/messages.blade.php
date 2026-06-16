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

.conversation-avatar .avatar-initials {
    font-size: 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
}

.conversation-avatar.male {
    background: #dbeafe;
}

.conversation-avatar.male .avatar-initials {
    color: #1e40af;
}

.conversation-avatar.female {
    background: #fce7f3;
}

.conversation-avatar.female .avatar-initials {
    color: #be185d;
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

    .conversation-item {
        padding: 1rem;
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
    }

    .conversation-avatar svg {
        width: 22px;
        height: 22px;
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
        
        <a href="{{ route('profile.index') }}" class="back-to-cabinet">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
            В личный кабинет
        </a>
    </div>

    @if(isset($conversations) && count($conversations) > 0)
        <!-- Список диалогов -->
        <div class="conversations-list">
            @foreach($conversations as $conversation)
                @php
                    $emailUser = explode('@', $conversation->interlocutor->email ?? '')[0];
                    $displayName = $conversation->interlocutor->display_name ?? ($conversation->interlocutor->fio ?: $emailUser);
                    $initials = mb_strtoupper(mb_substr($displayName, 0, 2));
                @endphp
                <a href="{{ route('profile.messages.chat', $conversation->interlocutor->id ?? 0) }}"
                   class="conversation-item {{ $conversation->unread_count > 0 ? 'unread' : '' }}">
                    <div class="conversation-avatar {{ ($conversation->interlocutor->sex ?? 1) == 1 ? 'male' : 'female' }}">
                        <span class="avatar-initials">{{ $initials }}</span>
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-header">
                            <div class="conversation-name">
                                {{ $displayName }}
                            </div>
                            <div class="conversation-time">
                                {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans() }}
                            </div>
                        </div>
                        <div class="conversation-message">
                            {{ Str::limit($conversation->last_message, 60) }}
                        </div>
                        <div class="conversation-meta">
                            @if(isset($conversation->post))
                                <div class="conversation-post">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
                                    </svg>
                                    {{ Str::limit($conversation->post->title, 30) }}
                                </div>
                            @endif
                            @if($conversation->unread_count > 0)
                                <div class="unread-badge">{{ $conversation->unread_count }} новых</div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Пустое состояние -->
        <div class="conversations-list">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M6,9H18V11H6M14,14H6V12H14M18,8H6V6H18"/>
                    </svg>
                </div>
                <div class="empty-title">Сообщений пока нет</div>
                <div class="empty-description">
                    Начните общение, написав пользователям из объявлений
                </div>
            </div>
        </div>
    @endif

</div>

@endsection

<script>
// Автообновление списка диалогов с паузой на неактивной вкладке
let isUpdating = false;
let updateInterval = null;

function updateConversationsList() {
    if (isUpdating) return;
    isUpdating = true;

    fetch('{{ route("profile.messages") }}')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newList = doc.querySelector('.conversations-list');
            const currentList = document.querySelector('.conversations-list');
            
            if (newList && currentList) {
                if (newList.innerHTML !== currentList.innerHTML) {
                    currentList.innerHTML = newList.innerHTML;
                }
            }
        })
        .catch(error => console.error('Ошибка обновления диалогов:', error))
        .finally(() => {
            isUpdating = false;
        });
}

// 🔥 УПРАВЛЕНИЕ ОБНОВЛЕНИЕМ ПРИ СМЕНЕ ВКЛАДКИ
function startUpdating() {
    if (!updateInterval) {
        updateInterval = setInterval(updateConversationsList, 5000);
        console.log('✅ Обновление диалогов запущено');
    }
}

function stopUpdating() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
        console.log('⏸️ Обновление диалогов остановлено');
    }
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopUpdating(); // Вкладка неактивна - СТОП
    } else {
        updateConversationsList(); // Сразу обновляем
        startUpdating(); // Возобновляем
    }
});

// Запускаем при загрузке
startUpdating();
</script>


