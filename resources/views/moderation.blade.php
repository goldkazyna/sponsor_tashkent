@extends('layouts.app')

@section('title', 'Модерация AI')

@section('content')

<style>
.mod-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.mod-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.mod-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.mod-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.mod-counter {
    background: #f1f5f9;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
}

.mod-counter span {
    color: #ef4444;
    font-weight: 700;
}

.mod-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
    font-size: 1.1rem;
}

.mod-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.mod-col {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
}

.mod-col-original {
    border-color: #fecaca;
    background: #fef7f7;
}

.mod-col-ai {
    border-color: #bbf7d0;
    background: #f0fdf4;
}

.mod-col-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.mod-col-original .mod-col-label {
    color: #dc2626;
    border-color: #fecaca;
}

.mod-col-ai .mod-col-label {
    color: #16a34a;
    border-color: #bbf7d0;
}

.mod-field-label {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}

.mod-field-value {
    color: #1a202c;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    white-space: pre-wrap;
    word-break: break-word;
}

.mod-field-value:last-child {
    margin-bottom: 0;
}

.mod-post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.mod-meta-item {
    background: #f1f5f9;
    padding: 0.3rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    color: #64748b;
}

.mod-actions {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    flex-wrap: wrap;
}

.mod-btn-approve {
    background: #16a34a;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.mod-btn-approve:hover {
    background: #15803d;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

.mod-rule-form {
    flex: 1;
    min-width: 300px;
}

.mod-rule-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.mod-rule-input:focus {
    outline: none;
    border-color: #3b82f6;
}

.mod-btn-rule {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.mod-btn-rule:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.mod-btn-skip {
    background: #f1f5f9;
    color: #64748b;
    border: 2px solid #e2e8f0;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.mod-btn-skip:hover {
    background: #e2e8f0;
    color: #475569;
}

.mod-rules-list {
    margin-top: 2rem;
    border-top: 2px solid #f1f5f9;
    padding-top: 1.5rem;
}

.mod-rules-list h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.75rem;
}

.mod-rules-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mod-rules-list li {
    padding: 0.4rem 0 0.4rem 1rem;
    position: relative;
    color: #475569;
    font-size: 0.85rem;
    line-height: 1.5;
}

.mod-rules-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.7rem;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #3b82f6;
}

.mod-success {
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    color: #16a34a;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .mod-container {
        padding: 0;
    }

    .mod-card {
        padding: 1.5rem;
    }

    .mod-columns {
        grid-template-columns: 1fr;
    }

    .mod-header {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .mod-actions {
        flex-direction: column;
    }

    .mod-rule-form {
        min-width: unset;
        width: 100%;
    }
}
</style>

<div class="mod-container">
    <div class="mod-card">

        <div class="mod-header">
            <h1>Модерация AI</h1>
            <div class="mod-counter">Осталось: <span>{{ $remaining }}</span></div>
        </div>

        @if(session('success'))
            <div class="mod-success">{{ session('success') }}</div>
        @endif

        @if($post)
            <div class="mod-post-meta">
                <div class="mod-meta-item">ID: {{ $post->id }}</div>
                <div class="mod-meta-item">{{ $post->who == 1 ? 'Спонсор ищет' : 'Содержанка ищет' }}</div>
                <div class="mod-meta-item">{{ $post->city ?? '—' }}</div>
                <div class="mod-meta-item">{{ $post->created_at ?? '' }}</div>
            </div>

            <div class="mod-columns">
                <div class="mod-col mod-col-original">
                    <div class="mod-col-label">Оригинал</div>
                    <div class="mod-field-label">Заголовок</div>
                    <div class="mod-field-value">{{ $post->title ?? '' }}</div>
                    <div class="mod-field-label">Описание</div>
                    <div class="mod-field-value">{{ $post->discription ?? '' }}</div>
                </div>
                <div class="mod-col mod-col-ai">
                    <div class="mod-col-label">AI версия</div>
                    <div class="mod-field-label">Заголовок</div>
                    <div class="mod-field-value">{{ $aiTitle }}</div>
                    <div class="mod-field-label">Описание</div>
                    <div class="mod-field-value">{{ $aiDescription }}</div>
                    @if($aiTelegram)
                        <div class="mod-field-label">Telegram (извлечён)</div>
                        <div class="mod-field-value">{{ $aiTelegram }}</div>
                    @endif
                </div>
            </div>

            @if($aiChanges)
                <div style="background: #fff7ed; border: 2px solid #f97316; border-radius: 10px; padding: 0.75rem 1rem; color: #c2410c; font-weight: 600; margin-bottom: 1rem; font-size: 0.9rem;">
                    Изменения: {{ $aiChanges }}
                </div>
            @endif

            @if($aiDelete)
                <div style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 10px; padding: 0.75rem 1rem; color: #dc2626; font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem;">
                    AI рекомендует УДАЛИТЬ: {{ $aiDeleteReason ?: 'запрещённый контент' }}
                </div>
            @endif

            <div class="mod-actions">
                {{-- Кнопка "Проверено" — сохраняет AI-версию в БД --}}
                <form method="POST" action="/moderation-secret/approve">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <input type="hidden" name="ai_title" value="{{ $aiTitle }}">
                    <input type="hidden" name="ai_description" value="{{ $aiDescription }}">
                    <input type="hidden" name="ai_telegram" value="{{ $aiTelegram }}">
                    <button type="submit" class="mod-btn-approve">Проверено</button>
                </form>

                {{-- Кнопка "Удалить" --}}
                <form method="POST" action="/moderation-secret/approve">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <input type="hidden" name="delete" value="1">
                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer;">Удалить</button>
                </form>

                {{-- Кнопка "Пропустить" --}}
                <form method="POST" action="/moderation-secret/approve">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <input type="hidden" name="skip" value="1">
                    <button type="submit" class="mod-btn-skip">Пропустить</button>
                </form>

                {{-- Форма добавления правила --}}
                <form method="POST" action="/moderation-secret/add-rule" class="mod-rule-form">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <input type="text" name="rule" class="mod-rule-input" placeholder="Комментарий для AI (новое правило)" required>
                    <button type="submit" class="mod-btn-rule">Добавить правило и пропустить</button>
                </form>
            </div>

        @else
            <div class="mod-empty">Все объявления проверены!</div>
        @endif

        {{-- Текущие правила --}}
        @if(!empty($rules))
            <div class="mod-rules-list">
                <h3>Текущие правила ({{ count($rules) }})</h3>
                <ul>
                    @foreach($rules as $rule)
                        <li>{{ $rule }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</div>

@endsection
