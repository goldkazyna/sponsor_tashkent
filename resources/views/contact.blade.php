@extends('layouts.app')

@section('title', 'Связаться с администрацией')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.contact-container {
    max-width: 700px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
}

.back-button:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.back-button svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.contact-form-wrapper {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.form-header p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
}

.privacy-notice {
    background: #f0f9ff;
    border: 2px solid #bfdbfe;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
}

.privacy-notice svg {
    width: 24px;
    height: 24px;
    fill: #2563eb;
    flex-shrink: 0;
}

.privacy-notice-text {
    color: #1e40af;
    font-size: 0.9rem;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
}

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: #1a202c;
    box-shadow: 0 0 0 3px rgba(26,32,44,0.1);
}

.form-input:read-only {
    background: #f8fafc;
    color: #64748b;
    cursor: not-allowed;
}

.form-textarea {
    resize: vertical;
    min-height: 150px;
}

.contact-type-wrapper {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 0.75rem;
}

.form-helper {
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.submit-button {
    width: 100%;
    padding: 1rem;
    background: #1a202c;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.submit-button:hover:not(:disabled) {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26,32,44,0.3);
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.submit-button svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.success-message,
.error-message {
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: none;
}

.success-message {
    background: #d1fae5;
    border: 2px solid #6ee7b7;
    color: #065f46;
}

.error-message {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
}

@media (max-width: 768px) {
    .contact-form-wrapper {
        padding: 1.75rem;
    }

    .form-header h1 {
        font-size: 1.5rem;
    }

    .contact-type-wrapper {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="contact-container">
    
    <!-- Кнопка назад -->
    <a href="{{ route('home') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        На главную
    </a>

    <!-- Форма -->
    <div class="contact-form-wrapper">
        
        <div class="form-header">
            <h1>Связаться с администрацией</h1>
            <p>Заполните форму ниже и мы свяжемся с вами в ближайшее время</p>
        </div>

        <!-- Уведомление о конфиденциальности -->
        <div class="privacy-notice">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M17.13,17C15.92,18.85 14.11,20.24 12,20.92C9.89,20.24 8.08,18.85 6.87,17C6.53,16.5 6.24,16 6,15.47C6,13.82 8.71,12.47 12,12.47C15.29,12.47 18,13.79 18,15.47C17.76,16 17.47,16.5 17.13,17Z"/>
            </svg>
            <div class="privacy-notice-text">
                <strong>Полная конфиденциальность:</strong> Ваши данные будут переданы только администрации сайта и никогда не будут доступны третьим лицам.
            </div>
        </div>

        <!-- Сообщения об успехе/ошибке -->
        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <form id="contactForm">
            @csrf
            
            <!-- Имя -->
            <div class="form-group">
                <label class="form-label">Имя</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-input" 
                    placeholder="Ваше имя"
                    value="{{ $user->fio ?? '' }}"
                >
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label required">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="your@email.com"
                    value="{{ $user->email ?? '' }}"
                    {{ $user ? 'readonly' : '' }}
                    required
                >
                @if($user)
                    <div class="form-helper">Email из вашего профиля</div>
                @endif
            </div>

            <!-- WhatsApp / Telegram -->
            <div class="form-group">
                <p style="color: #dc2626; font-size: 0.85rem; font-weight: 500; margin: 0 0 0.5rem 0;">Пожалуйста, оставьте Telegram или WhatsApp для связи, чтобы мы могли с вами связаться быстро!</p>
                <label class="form-label">Контакт для связи</label>
                <div class="contact-type-wrapper">
                    <select name="contact_type" class="form-select">
                        <option value="telegram">Telegram</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                    <input
                        type="text"
                        name="contact_value"
                        class="form-input"
                        placeholder="@username или +77001234567"
                    >
                </div>
            </div>

            <!-- Сообщение -->
            <div class="form-group">
                <label class="form-label required">Сообщение</label>
                <textarea 
                    name="message" 
                    class="form-textarea" 
                    placeholder="Опишите ваш вопрос или проблему..."
                    required
                ></textarea>
                <div class="form-helper">Минимум 10 символов</div>
            </div>

            <!-- Кнопка отправки -->
            <button type="submit" class="submit-button" id="submitBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
                Отправить сообщение
            </button>

        </form>

    </div>

</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    
    // Скрываем предыдущие сообщения
    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';
    
    // Блокируем кнопку
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Отправка...';
    
    // Собираем данные
    const data = {
        name: document.querySelector('[name="name"]').value,
        email: document.querySelector('[name="email"]').value,
        contact_type: document.querySelector('[name="contact_type"]').value,
        contact_value: document.querySelector('[name="contact_value"]').value,
        message: document.querySelector('[name="message"]').value,
    };
    
    fetch('{{ route("contact.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Status:', response.status);
        if (!response.ok && response.status !== 422) {
            throw new Error('Network error');
        }
        return response.json();
    })
    .then(data => {
        console.log('Data:', data);
        if (data.success) {
            // Успех
            successMsg.textContent = data.message;
            successMsg.style.display = 'block';
            
            // Очищаем форму
            document.getElementById('contactForm').reset();
            
            // Прокручиваем к сообщению
            successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            // Ошибка
            errorMsg.textContent = data.message || 'Произошла ошибка при отправке';
            errorMsg.style.display = 'block';
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorMsg.textContent = 'Произошла ошибка. Попробуйте позже.';
        errorMsg.style.display = 'block';
    })
    .finally(() => {
        // Разблокируем кнопку
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg> Отправить сообщение';
    });
});
</script>

@endsection