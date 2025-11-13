@extends('layouts.app')

@section('title', 'Чат с Анной Петровой')

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
    background: #fce7f3;
    color: #be185d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
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
    background: #fce7f3;
}

.message.other .message-avatar svg {
    fill: #be185d;
}

.message.own .message-avatar {
    background: #dbeafe;
}

.message.own .message-avatar svg {
    fill: #1e40af;
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

.typing-indicator {
    display: none;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #94a3b8;
    font-size: 0.85rem;
    font-style: italic;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
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
        
        <div class="chat-user-info">
            <div class="chat-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #be185d;">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="chat-user-details">
                <h2>Анна Петрова</h2>
                <div class="chat-user-status">Была в сети 5 минут назад</div>
            </div>
        </div>

        <a href="/posts/ischu-sponsora-v-tashkente" class="chat-post-link" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2Z"/>
            </svg>
            Объявление
        </a>
    </div>

    <!-- Сообщения -->
    <div class="chat-messages" id="chatMessages">
        
        <!-- Разделитель даты -->
        <div class="message-date-divider">
            <span>Сегодня, 13 ноября</span>
        </div>

        <!-- Сообщение от собеседника -->
        <div class="message-group">
            <div class="message other">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Привет! Я видела ваше объявление.
                    </div>
                    <div class="message-time">10:15</div>
                </div>
            </div>

            <div class="message other">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Можем обсудить детали? Меня интересует серьёзное знакомство.
                    </div>
                    <div class="message-time">10:16</div>
                </div>
            </div>
        </div>

        <!-- Моё сообщение -->
        <div class="message-group">
            <div class="message own">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Здравствуйте, Анна! Да, конечно, давайте обсудим.
                    </div>
                    <div class="message-time">10:20</div>
                </div>
            </div>

            <div class="message own">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Что именно вас интересует? Можем встретиться на этой неделе.
                    </div>
                    <div class="message-time">10:21</div>
                </div>
            </div>
        </div>

        <!-- Сообщение от собеседника -->
        <div class="message-group">
            <div class="message other">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Отлично! Меня интересуют взаимовыгодные отношения.
                    </div>
                    <div class="message-time">10:25</div>
                </div>
            </div>

            <div class="message other">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Я свободна в четверг или пятницу вечером. Где удобно встретиться?
                    </div>
                    <div class="message-time">10:26</div>
                </div>
            </div>
        </div>

        <!-- Разделитель даты (вчера) -->
        <div class="message-date-divider">
            <span>Вчера, 12 ноября</span>
        </div>

        <!-- Старые сообщения -->
        <div class="message-group">
            <div class="message own">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Добрый вечер! Рад знакомству 😊
                    </div>
                    <div class="message-time">19:30</div>
                </div>
            </div>
        </div>

        <div class="message-group">
            <div class="message other">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Взаимно! Расскажите немного о себе.
                    </div>
                    <div class="message-time">19:45</div>
                </div>
            </div>
        </div>

        <div class="message-group">
            <div class="message own">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Мне 35 лет, занимаюсь бизнесом. Ищу приятную девушку для совместного времяпрепровождения.
                    </div>
                    <div class="message-time">19:50</div>
                </div>
            </div>

            <div class="message own">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Люблю путешествовать, хорошо проводить время.
                    </div>
                    <div class="message-time">19:51</div>
                </div>
            </div>
        </div>

        <!-- Индикатор печатания (скрыт по умолчанию) -->
        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
            <span>Анна печатает...</span>
        </div>

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

// Прокручиваем при загрузке
scrollToBottom();

// Обработка формы
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message) {
        // Здесь будет AJAX отправка сообщения
        console.log('Отправка сообщения:', message);
        
        // Очищаем поле
        input.value = '';
        
        // Пример: показываем индикатор печатания через 2 секунды
        setTimeout(() => {
            document.getElementById('typingIndicator').style.display = 'flex';
            scrollToBottom();
            
            // Скрываем через 3 секунды
            setTimeout(() => {
                document.getElementById('typingIndicator').style.display = 'none';
            }, 3000);
        }, 2000);
    }
});

// Автоматическое изменение высоты textarea
document.getElementById('messageInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Enter для отправки, Shift+Enter для новой строки
document.getElementById('messageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('messageForm').dispatchEvent(new Event('submit'));
    }
});
</script>

@endsection