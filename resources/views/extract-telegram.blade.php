@extends('layouts.app')

@section('title', 'Извлечение Telegram')

@section('content')

<style>
.et-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.et-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.et-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.et-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.et-counter {
    background: #f1f5f9;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
}

.et-counter span {
    color: #3b82f6;
    font-weight: 700;
}

.et-success {
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #16a34a;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.et-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
    font-size: 1.1rem;
}

.et-post {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: border-color 0.2s;
}

.et-post.has-telegram {
    border-color: #3b82f6;
    background: #f0f7ff;
}

.et-post.no-telegram {
    border-color: #e2e8f0;
    background: #fafafa;
}

.et-post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.et-post-id {
    background: #f1f5f9;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 600;
}

.et-found-badge {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}

.et-field {
    margin-bottom: 0.75rem;
}

.et-field-label {
    font-size: 0.7rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.2rem;
}

.et-field-value {
    color: #1a202c;
    font-size: 0.9rem;
    line-height: 1.5;
    white-space: pre-wrap;
    word-break: break-word;
}

.et-telegram-result {
    background: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.et-telegram-nick {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1d4ed8;
}

.et-telegram-actions {
    display: flex;
    gap: 0.5rem;
}

.et-btn-save {
    background: #16a34a;
    color: white;
    border: none;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.et-btn-save:hover {
    background: #15803d;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(22,163,74,0.3);
}

.et-btn-skip {
    background: #f1f5f9;
    color: #64748b;
    border: 2px solid #e2e8f0;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.et-btn-skip:hover {
    background: #e2e8f0;
    color: #475569;
}

.et-no-result {
    color: #94a3b8;
    font-size: 0.85rem;
    font-style: italic;
    margin-top: 0.75rem;
}

@media (max-width: 768px) {
    .et-container { padding: 0; }
    .et-card { padding: 1.5rem; }
    .et-header { flex-direction: column; gap: 0.75rem; align-items: flex-start; }
    .et-telegram-result { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="et-container">
    <div class="et-card">

        <div class="et-header">
            <h1>Извлечение Telegram</h1>
            <div class="et-counter">Без телеграма: <span>{{ $remaining }}</span></div>
        </div>

        @if(session('success'))
            <div class="et-success">{{ session('success') }}</div>
        @endif

        @if($posts->isEmpty())
            <div class="et-empty">Все объявления уже с телеграмом!</div>
        @else
            @foreach($posts as $post)
                @php
                    $ai = $aiResults[$post->id] ?? null;
                @endphp
                <div class="et-post {{ $ai ? 'has-telegram' : 'no-telegram' }}">
                    <div class="et-post-header">
                        <div class="et-post-id">ID: {{ $post->id }}</div>
                        @if($ai)
                            <div class="et-found-badge">Найден в {{ $ai['found_in'] }}</div>
                        @endif
                    </div>

                    <div class="et-field">
                        <div class="et-field-label">Заголовок</div>
                        <div class="et-field-value">{{ $post->title ?? '—' }}</div>
                    </div>

                    <div class="et-field">
                        <div class="et-field-label">ФИО</div>
                        <div class="et-field-value">{{ $post->fio ?? '—' }}</div>
                    </div>

                    <div class="et-field">
                        <div class="et-field-label">Описание</div>
                        <div class="et-field-value">{{ Str::limit($post->discription ?? '—', 300) }}</div>
                    </div>

                    @if($ai)
                        <div class="et-telegram-result">
                            <div class="et-telegram-nick">@{{ $ai['telegram'] }}</div>
                            <div class="et-telegram-actions">
                                <form method="POST" action="/extract-telegram-secret/save" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="telegram" value="{{ $ai['telegram'] }}">
                                    <input type="hidden" name="action" value="save">
                                    <button type="submit" class="et-btn-save">Сохранить</button>
                                </form>
                                <form method="POST" action="/extract-telegram-secret/save" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="action" value="skip">
                                    <button type="submit" class="et-btn-skip">Пропустить</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="et-no-result">Telegram не найден</div>
                    @endif
                </div>
            @endforeach
        @endif

    </div>
</div>

@endsection
