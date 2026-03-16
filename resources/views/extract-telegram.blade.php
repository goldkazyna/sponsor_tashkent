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
    flex-wrap: wrap;
    gap: 0.75rem;
}

.et-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.et-header-info {
    display: flex;
    gap: 0.75rem;
    align-items: center;
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
    flex-wrap: wrap;
    gap: 0.5rem;
}

.et-post-id {
    background: #f1f5f9;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 600;
}

.et-existing-tg {
    background: #fef3c7;
    color: #92400e;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
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

.et-no-result {
    color: #94a3b8;
    font-size: 0.85rem;
    font-style: italic;
    margin-top: 0.75rem;
}

/* Пагинация */
.et-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.et-page-link {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    color: #64748b;
    background: #f1f5f9;
    border: 2px solid #e2e8f0;
    transition: all 0.2s;
}

.et-page-link:hover {
    background: #e2e8f0;
    color: #1a202c;
}

.et-page-link.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.et-page-link.disabled {
    opacity: 0.4;
    pointer-events: none;
}

@media (max-width: 768px) {
    .et-container { padding: 0; }
    .et-card { padding: 1.5rem; }
    .et-header { flex-direction: column; align-items: flex-start; }
    .et-telegram-result { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="et-container">
    <div class="et-card">

        <div class="et-header">
            <h1>Извлечение Telegram</h1>
        </div>

        @if(session('success'))
            <div class="et-success">{{ session('success') }}</div>
        @endif

        @if($posts->isEmpty())
            <div class="et-empty">Объявлений нет</div>
        @else
            @foreach($posts as $post)
                @php
                    $ai = $aiResults[$post->id] ?? null;
                @endphp
                <div class="et-post {{ $ai ? 'has-telegram' : 'no-telegram' }}">
                    <div class="et-post-header">
                        <div class="et-post-id">ID: {{ $post->id }}</div>
                        @if(!empty($post->telegram))
                            <div class="et-existing-tg">TG: {{ '@' . $post->telegram }}</div>
                        @endif
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
                            <div>
                                <div class="et-telegram-nick">@{{ $ai['telegram'] }}</div>
                                @if(!empty($post->telegram))
                                    <div style="font-size:0.8rem;color:#64748b;margin-top:0.25rem">В поле уже есть: {{ '@' . $post->telegram }} — ник будет только вырезан из текста</div>
                                @endif
                            </div>
                            <form method="POST" action="/extract-telegram-secret/save" style="display:inline">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                <input type="hidden" name="telegram" value="{{ $ai['telegram'] }}">
                                <button type="submit" class="et-btn-save">{{ empty($post->telegram) ? 'Сохранить и вырезать' : 'Вырезать из текста' }}</button>
                            </form>
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
