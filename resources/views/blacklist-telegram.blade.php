@extends('layouts.app')

@section('title', 'Чёрный список Telegram')

@section('content')

<style>
.bl-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.bl-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.bl-card h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0 0 1.5rem;
}

.bl-success {
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #16a34a;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.bl-add-form {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.bl-add-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
}

.bl-add-input:focus {
    outline: none;
    border-color: #ef4444;
}

.bl-btn-add {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.bl-btn-add:hover {
    background: #dc2626;
}

.bl-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.bl-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    border: 1px solid #fecaca;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: #fef2f2;
}

.bl-nick {
    font-weight: 600;
    color: #991b1b;
    font-size: 0.95rem;
}

.bl-btn-remove {
    background: none;
    border: 1px solid #fca5a5;
    color: #dc2626;
    padding: 0.3rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
}

.bl-btn-remove:hover {
    background: #fecaca;
}

.bl-empty {
    color: #94a3b8;
    font-style: italic;
    text-align: center;
    padding: 2rem;
}

.bl-info {
    background: #f1f5f9;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}
</style>

<div class="bl-container">
    <div class="bl-card">
        <h1>Чёрный список Telegram</h1>

        <div class="bl-info">
            При публикации объявления проверяются заголовок, ФИО, описание и поле Telegram.
            Если найден ник из списка — аккаунт тихо блокируется (password = 1), без уведомлений.
        </div>

        @if(session('success'))
            <div class="bl-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="/blacklist-telegram-secret/add" class="bl-add-form">
            @csrf
            <input type="text" name="nick" class="bl-add-input" placeholder="Telegram-ник (без @)" required>
            <button type="submit" class="bl-btn-add">Добавить</button>
        </form>

        @if(empty($blacklist))
            <div class="bl-empty">Список пуст</div>
        @else
            <ul class="bl-list">
                @foreach($blacklist as $nick)
                    <li class="bl-item">
                        <span class="bl-nick">@{{ $nick }}</span>
                        <form method="POST" action="/blacklist-telegram-secret/remove" style="display:inline">
                            @csrf
                            <input type="hidden" name="nick" value="{{ $nick }}">
                            <button type="submit" class="bl-btn-remove">Удалить</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@endsection
