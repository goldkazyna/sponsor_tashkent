@extends('layouts.app')

@section('title', 'Поднять объявление в ТОП')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.boost-container {
    max-width: 900px;
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

.boost-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.boost-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.75rem;
}

.boost-header p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.post-info-card {
    background: #fffbeb;
    border: 2px solid #fcd34d;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.post-info-card svg {
    width: 20px;
    height: 20px;
    fill: #d97706;
    flex-shrink: 0;
}

.post-info-card span {
    font-size: 0.9rem;
    color: #92400e;
}

.post-info-card strong {
    color: #78350f;
}

.tariffs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.tariff-card {
    background: white;
    border-radius: 16px;
    padding: 2rem 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    text-align: center;
    position: relative;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.tariff-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.tariff-card.popular {
    border-color: #f59e0b;
}

.tariff-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    padding: 0.3rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.tariff-days {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.tariff-days-label {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.tariff-price {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.25rem;
}

.tariff-price-usd {
    color: #94a3b8;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
}

.tariff-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem;
    text-align: left;
}

.tariff-features li {
    padding: 0.4rem 0;
    font-size: 0.85rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tariff-features li svg {
    width: 16px;
    height: 16px;
    fill: #f59e0b;
    flex-shrink: 0;
}

.tariff-btn {
    width: 100%;
    padding: 0.85rem 1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    background: #1a202c;
}

.tariff-btn:hover {
    background: #2d3748;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(26,32,44,0.3);
}

.tariff-card.popular .tariff-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.tariff-card.popular .tariff-btn:hover {
    box-shadow: 0 4px 12px rgba(245,158,11,0.4);
}

.tariff-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.boost-info {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.boost-info h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.info-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
    color: #475569;
}

.info-list li svg {
    width: 20px;
    height: 20px;
    fill: #f59e0b;
    flex-shrink: 0;
}

.error-message {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.help-form-wrapper {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.help-form-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.help-form-header h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.help-form-header p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.6;
}

.form-group { margin-bottom: 1.25rem; }

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

.form-input, .form-textarea {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #1a202c;
    box-shadow: 0 0 0 3px rgba(26,32,44,0.1);
}

.form-input:read-only {
    background: #f8fafc;
    color: #64748b;
    cursor: not-allowed;
}

.form-textarea { resize: vertical; min-height: 100px; }

.form-helper { font-size: 0.8rem; color: #94a3b8; margin-top: 0.5rem; }

.submit-button {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
}

.submit-button:disabled { opacity: 0.6; cursor: not-allowed; }

.submit-button svg { width: 20px; height: 20px; fill: white; }

.success-message, .error-message-form {
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

.error-message-form {
    background: #fee2e2;
    border: 2px solid #fca5a5;
    color: #991b1b;
}

@media (max-width: 768px) {
    .boost-container { padding: 0 1rem; }
    .tariffs-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
    .info-list { grid-template-columns: 1fr; }
    .boost-header h1 { font-size: 1.4rem; }
    .help-form-wrapper { padding: 1.75rem; }
}
</style>

<div class="boost-container">

    <a href="{{ route('profile.posts') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        Мои объявления
    </a>

    <div class="boost-header">
        <h1>Поднять объявление в ТОП</h1>
        <p>Ваше объявление будет показываться первым в специальной секции на главной странице. Выберите срок:</p>
    </div>

    @if($post)
    <div class="post-info-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
        </svg>
        <span>Объявление: <strong>{{ $post->title }}</strong> (ID: {{ $post->id }})</span>
    </div>
    @endif

    @if(session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    <div class="tariffs-grid">

        {{-- 5 дней --}}
        <div class="tariff-card">
            <div class="tariff-days">5</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">7 592 ₸</div>
            <div class="tariff-price-usd">~ $14</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Показ в секции ТОП
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Первым на главной
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Больше просмотров
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="4000">
                <input type="hidden" name="days" value="5">
                <input type="hidden" name="service" value="top_post">
                <input type="hidden" name="post_id" value="{{ $post->id ?? '' }}">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

        {{-- 10 дней --}}
        <div class="tariff-card">
            <div class="tariff-days">10</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">10 846 ₸</div>
            <div class="tariff-price-usd">~ $20</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Показ в секции ТОП
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Первым на главной
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Больше просмотров
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="10846">
                <input type="hidden" name="days" value="10">
                <input type="hidden" name="service" value="top_post">
                <input type="hidden" name="post_id" value="{{ $post->id ?? '' }}">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

        {{-- 30 дней --}}
        <div class="tariff-card popular">
            <div class="tariff-badge">Популярный</div>
            <div class="tariff-days">30</div>
            <div class="tariff-days-label">дней</div>
            <div class="tariff-price">16 268 ₸</div>
            <div class="tariff-price-usd">~ $30</div>
            <ul class="tariff-features">
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Показ в секции ТОП
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Первым на главной
                </li>
                <li>
                    <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                    Максимальная выгода
                </li>
            </ul>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="16268">
                <input type="hidden" name="days" value="30">
                <input type="hidden" name="service" value="top_post">
                <input type="hidden" name="post_id" value="{{ $post->id ?? '' }}">
                <button type="submit" class="tariff-btn">Оплатить</button>
            </form>
        </div>

    </div>

    <div class="boost-info">
        <h3>Что даёт размещение в ТОП?</h3>
        <ul class="info-list">
            <li>
                <svg viewBox="0 0 24 24"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
                Показ в специальной секции на главной
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M15,13H16.5V15.82L18.94,17.23L18.19,18.53L15,16.69V13M19,8H5V19H9.67C9.24,18.09 9,17.07 9,16A7,7 0 0,1 16,9C17.07,9 18.09,9.24 19,9.67V8M16,11A5,5 0 0,0 11,16A5,5 0 0,0 16,21A5,5 0 0,0 21,16A5,5 0 0,0 16,11Z"/></svg>
                Объявление видно первым
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z"/></svg>
                Кратное увеличение просмотров
            </li>
            <li>
                <svg viewBox="0 0 24 24"><path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M20,16H6L4,18V4H20"/></svg>
                Больше откликов и сообщений
            </li>
        </ul>
    </div>

    {{-- Форма помощи --}}
    <div class="help-form-wrapper">

        <div class="help-form-header">
            <h3>Нужна помощь с оплатой?</h3>
            <p>Заполните форму ниже или напишите напрямую нам в Telegram — <a href="https://t.me/Sponsor_admin" target="_blank" style="color: #f59e0b; font-weight: 600; text-decoration: none;">@Sponsor_admin</a></p>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message-form" id="errorMessage"></div>

        <form id="helpForm">
            @csrf

            <input type="hidden" name="post_id" value="{{ $post->id ?? '' }}">

            <div class="form-group">
                <label class="form-label">Имя</label>
                <input type="text" name="name" class="form-input" placeholder="Ваше имя" value="{{ $user->fio ?? '' }}">
            </div>

            <div class="form-group">
                <label class="form-label required">Email</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" value="{{ $user->email ?? '' }}" {{ $user ? 'readonly' : '' }} required>
                @if($user)
                    <div class="form-helper">Email из вашего профиля</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label required">Ваш Telegram</label>
                <input type="text" name="telegram" class="form-input" placeholder="@username" value="{{ ($user->telegram_username ?? null) ? '@' . $user->telegram_username : '' }}" required>
                <div class="form-helper">Укажите Telegram — мы свяжемся с вами для помощи</div>
            </div>

            <div class="form-group">
                <label class="form-label">Комментарий</label>
                <textarea name="message" class="form-textarea" placeholder="Опишите вашу проблему или вопрос..."></textarea>
            </div>

            <button type="submit" class="submit-button" id="submitBtn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                </svg>
                Отправить
            </button>

        </form>

    </div>

</div>

<script>
document.getElementById('helpForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;animation:spin 1s linear infinite;"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Отправка...';

    const data = {
        name: this.querySelector('[name="name"]').value,
        email: this.querySelector('[name="email"]').value,
        telegram: this.querySelector('[name="telegram"]').value,
        post_id: this.querySelector('[name="post_id"]').value,
        message: this.querySelector('[name="message"]').value,
    };

    fetch('{{ route("boost.top.send") }}', {
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
            document.getElementById('helpForm').reset();
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
        submitBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg> Отправить';
    });
});
</script>

@endsection
