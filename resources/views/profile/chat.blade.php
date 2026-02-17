@extends('layouts.app')

@section('title', 'Чат с ' . ($interlocutor->fio ?: explode('@', $interlocutor->email ?? '')[0]))

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.chat-container {
    max-width: 1000px;
    margin: 0 auto;
}

.chat-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-button {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f8fafc;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.back-button:hover {
    background: #e2e8f0;
}

.back-button svg {
    width: 20px;
    height: 20px;
    fill: #1a202c;
}

.chat-user-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.chat-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: {{ $interlocutor->sex == 1 ? '#dbeafe' : '#fce7f3' }};
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.chat-avatar .avatar-initials {
    font-size: 1.1rem;
    font-weight: 700;
    color: {{ $interlocutor->sex == 1 ? '#1e40af' : '#be185d' }};
    text-transform: uppercase;
}

.chat-user-details h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.2rem;
}

.chat-user-status {
    font-size: 0.85rem;
    color: #94a3b8;
}

.chat-post-link {
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

.chat-post-link:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.chat-post-link svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.chat-messages {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    min-height: 500px;
    max-height: 600px;
    overflow-y: auto;
}

.message-date-divider {
    text-align: center;
    margin: 2rem 0 1.5rem;
    position: relative;
}

.message-date-divider span {
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 600;
    border: 2px solid #f1f5f9;
}

.message-group {
    margin-bottom: 1.5rem;
}

.message {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.message.own {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.message-avatar svg {
    width: 20px;
    height: 20px;
}

.message.other .message-avatar {
    background: {{ $interlocutor->sex == 1 ? '#dbeafe' : '#fce7f3' }};
}

.message.other .message-avatar svg {
    fill: {{ $interlocutor->sex == 1 ? '#1e40af' : '#be185d' }};
}

.message.own .message-avatar {
    background: {{ $user->sex == 1 ? '#dbeafe' : '#fce7f3' }};
}

.message.own .message-avatar svg {
    fill: {{ $user->sex == 1 ? '#1e40af' : '#be185d' }};
}

.message-content {
    max-width: 65%;
}

.message.own .message-content {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.message-bubble {
    padding: 0.75rem 1rem;
    border-radius: 12px;
    margin-bottom: 0.3rem;
    word-wrap: break-word;
}

.message.other .message-bubble {
    background: #f1f5f9;
    color: #1a202c;
    border-bottom-left-radius: 4px;
}

.message.own .message-bubble {
    background: #1a202c;
    color: white;
    border-bottom-right-radius: 4px;
}

.message-time {
    font-size: 0.75rem;
    color: #94a3b8;
    padding: 0 0.5rem;
}

.message.own .message-time {
    text-align: right;
}

.chat-input-form {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.input-wrapper {
    display: flex;
    gap: 1rem;
    align-items: end;
}

.message-textarea {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    resize: none;
    min-height: 60px;
    max-height: 150px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.message-textarea:focus {
    outline: none;
    border-color: #1a202c;
}

.send-button {
    padding: 0.75rem 2rem;
    background: #1a202c;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    height: 60px;
}

.send-button:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26, 32, 44, 0.3);
}

.send-button svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.empty-messages {
    text-align: center;
    padding: 3rem;
    color: #94a3b8;
}

@media (max-width: 768px) {
    .chat-header {
        padding: 1rem;
        flex-wrap: wrap;
    }

    .chat-user-info {
        flex: 1 1 100%;
        order: 1;
    }

    .back-button {
        order: 0;
    }

    .chat-post-link {
        order: 2;
        flex: 1 1 100%;
        margin-top: 0.5rem;
        justify-content: center;
    }

    .chat-messages {
        padding: 1rem;
        min-height: 400px;
        max-height: 500px;
    }

    .message-content {
        max-width: 80%;
    }

    .chat-input-form {
        padding: 1rem;
    }

    .input-wrapper {
        flex-direction: column;
        gap: 0.75rem;
    }

    .message-textarea {
        width: 100%;
        min-height: 80px;
    }

    .send-button {
        width: 100%;
        justify-content: center;
        height: 50px;
    }
}
</style>

<div class="chat-container">
    
    <!-- Шапка чата -->
    <div class="chat-header">
        <a href="{{ route('profile.messages') }}" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
        </a>
        
        @php
            $emailUser = explode('@', $interlocutor->email ?? '')[0];
            $chatDisplayName = $interlocutor->fio ?: $emailUser;
            $chatInitials = mb_strtoupper(mb_substr($chatDisplayName, 0, 2));
        @endphp
        <div class="chat-user-info">
            <div class="chat-avatar">
                <span class="avatar-initials">{{ $chatInitials }}</span>
            </div>
            <div class="chat-user-details">
                <h2>{{ $chatDisplayName }}</h2>
                <div class="chat-user-status">{{ $interlocutor->sex == 1 ? 'Спонсор' : 'Содержанка' }}</div>
            </div>
        </div>

        @if($post)
        <a href="/post/detail/{{ $post->id }}" class="chat-post-link" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
            </svg>
            Объявление
        </a>
        @endif
    </div>

    <!-- Сообщения -->
    <div class="chat-messages" id="chatMessages">
        
        @if($messages->isEmpty())
            <div class="empty-messages">
                <p>Начните переписку - напишите первое сообщение!</p>
            </div>
        @else
            @php
                $currentDate = null;
            @endphp
            
            @foreach($messages as $message)
                @php
                    $messageDate = \Carbon\Carbon::parse($message->created_at)->format('Y-m-d');
                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                    $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');
                    
                    if ($currentDate != $messageDate) {
                        $currentDate = $messageDate;
                        if ($messageDate == $today) {
                            $dateLabel = 'Сегодня';
                        } elseif ($messageDate == $yesterday) {
                            $dateLabel = 'Вчера';
                        } else {
                            $dateLabel = \Carbon\Carbon::parse($message->created_at)->locale('ru')->isoFormat('D MMMM');
                        }
                    } else {
                        $dateLabel = null;
                    }
                @endphp
                
                @if($dateLabel)
                    <div class="message-date-divider">
                        <span>{{ $dateLabel }}</span>
                    </div>
                @endif
                
                <div class="message {{ $message->sender_id == $user->id ? 'own' : 'other' }}">
                    <div class="message-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            {{ $message->message }}
                        </div>
                        <div class="message-time">
                            {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    <!-- Форма отправки -->
    <form class="chat-input-form" id="messageForm">
        <div class="input-wrapper">
            <textarea 
                class="message-textarea" 
                id="messageInput"
                placeholder="Введите сообщение..."
                rows="2"
            ></textarea>
            <button type="submit" class="send-button">
                <span>Отправить</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
            </button>
        </div>
    </form>

</div>

<script>
// Автоматическая прокрутка вниз
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

scrollToBottom();

// Long Polling с паузой на неактивной вкладке
let lastMessageId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
let isPolling = false;
let pollingInterval = null;

function pollNewMessages() {
    if (isPolling) return;
    isPolling = true;

    fetch(`/profile/messages/new/{{ $interlocutor->id }}?last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    addMessageToChat(msg);
                    lastMessageId = msg.id;
                });
                scrollToBottom();
            }
        })
        .catch(error => console.error('Ошибка Long Polling:', error))
        .finally(() => {
            isPolling = false;
        });
}

// Добавление сообщения в чат
function addMessageToChat(msg) {
    const chatMessages = document.getElementById('chatMessages');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message other';
    
    messageDiv.innerHTML = `
        <div class="message-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
            </svg>
        </div>
        <div class="message-content">
            <div class="message-bubble">
                ${escapeHtml(msg.message)}
            </div>
            <div class="message-time">${formatTime(msg.created_at)}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
}

function formatTime(datetime) {
    const date = new Date(datetime);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 🔥 УПРАВЛЕНИЕ POLLING ПРИ СМЕНЕ ВКЛАДКИ
function startPolling() {
    if (!pollingInterval) {
        pollingInterval = setInterval(pollNewMessages, 3000);
        console.log('✅ Polling запущен (вкладка активна)');
    }
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
        console.log('⏸️ Polling остановлен (вкладка неактивна)');
    }
}

// Запускаем или останавливаем polling в зависимости от видимости вкладки
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPolling(); // Вкладка неактивна - СТОП
    } else {
        pollNewMessages(); // Сразу обновляем
        startPolling(); // Возобновляем polling
    }
});

// Запускаем при загрузке страницы
startPolling();

// Обработка формы отправки
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message) {
        fetch('{{ route("profile.messages.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                receiver_id: {{ $interlocutor->id }},
                message: message,
                post_id: {{ $post->id ?? 'null' }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addOwnMessageToChat(data.message);
                input.value = '';
                input.style.height = 'auto';
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Ошибка отправки:', error);
            alert('Ошибка при отправке сообщения');
        });
    }
});

function addOwnMessageToChat(msg) {
    const chatMessages = document.getElementById('chatMessages');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message own';
    
    messageDiv.innerHTML = `
        <div class="message-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
            </svg>
        </div>
        <div class="message-content">
            <div class="message-bubble">
                ${escapeHtml(msg.message)}
            </div>
            <div class="message-time">${formatTime(msg.created_at)}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    lastMessageId = msg.id;
}

document.getElementById('messageInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('messageForm').dispatchEvent(new Event('submit'));
    }
});
</script>




@endsection