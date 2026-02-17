@extends('layouts.app')

@section('title', 'Купить статус проверенного пользователя')

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

.tariff-badge {
    display: inline-block;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 0.75rem;
}

.warning-notice {
    background: #fffbeb;
    border: 2px solid #fcd34d;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
}

.warning-notice svg {
    width: 24px;
    height: 24px;
    fill: #d97706;
    flex-shrink: 0;
    margin-top: 2px;
}

.warning-notice-text {
    color: #92400e;
    font-size: 0.9rem;
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
.form-textarea {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
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
    min-height: 100px;
}

.form-helper {
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.submit-button {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
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
    .contact-container {
        padding: 0;
    }

    .contact-form-wrapper {
        padding: 1.75rem;
    }

    .form-header h1 {
        font-size: 1.5rem;
    }
}
</style>

<div class="contact-container">

    <a href="{{ route('home') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        На главную
    </a>

    <div class="contact-form-wrapper">

        <div class="form-header">
            <h1>Купить статус проверенного пользователя</h1>
            <p>Получите значок проверенного пользователя и повысьте доверие к вашему профилю</p>
            <div class="tariff-badge">Тариф: 30 дней</div>
        </div>

        <!-- Предупреждение -->
        <div class="warning-notice">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
            </svg>
            <div class="warning-notice-text">
                <strong>Важно!</strong> В связи с постоянными блокировками карт, оплата производится по следующей схеме: заполните форму ниже и <strong>обязательно укажите ваш Telegram</strong>. Мы свяжемся с вами и сообщим реквизиты для перевода.
            </div>
        </div>

        <!-- Конфиденциальность -->
        <div class="privacy-notice">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M17.13,17C15.92,18.85 14.11,20.24 12,20.92C9.89,20.24 8.08,18.85 6.87,17C6.53,16.5 6.24,16 6,15.47C6,13.82 8.71,12.47 12,12.47C15.29,12.47 18,13.79 18,15.47C17.76,16 17.47,16.5 17.13,17Z"/>
            </svg>
            <div class="privacy-notice-text">
                <strong>Полная конфиденциальность:</strong> Ваши данные будут переданы только администрации сайта и никогда не будут доступны третьим лицам.
            </div>
        </div>

        <!-- Сообщения -->
        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <form id="verifiedForm">
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

            <!-- Telegram -->
            <div class="form-group">
                <label class="form-label required">Ваш Telegram</label>
                <input
                    type="text"
                    name="telegram"
                    class="form-input"
                    placeholder="@username"
                    value="{{ ($user->telegram_username ?? null) ? '@' . $user->telegram_username : '' }}"
                    required
                >
                <div class="form-helper">Обязательно укажите Telegram — мы свяжемся с вами для уточнения реквизитов оплаты</div>
            </div>

            <!-- Комментарий -->
            <div class="form-group">
                <label class="form-label">Комментарий</label>
                <textarea
                    name="message"
                    class="form-textarea"
                    placeholder="Дополнительная информация (необязательно)..."
                ></textarea>
            </div>

            <!-- Кнопка -->
            <button type="submit" class="submit-button" id="submitBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
                Отправить заявку
            </button>

        </form>

    </div>

</div>

<script>
document.getElementById('verifiedForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Отправка...';

    const data = {
        name: document.querySelector('[name="name"]').value,
        email: document.querySelector('[name="email"]').value,
        telegram: document.querySelector('[name="telegram"]').value,
        message: document.querySelector('[name="message"]').value,
    };

    fetch('{{ route("become.verified.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok && response.status !== 422) {
            throw new Error('Network error');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            successMsg.textContent = data.message;
            successMsg.style.display = 'block';
            document.getElementById('verifiedForm').reset();
            successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            errorMsg.textContent = data.message || 'Произошла ошибка при отправке';
            errorMsg.style.display = 'block';
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .catch(error => {
        errorMsg.textContent = 'Произошла ошибка. Попробуйте позже.';
        errorMsg.style.display = 'block';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/></svg> Отправить заявку';
    });
});
</script>

@endsection
