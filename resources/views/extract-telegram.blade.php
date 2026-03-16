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

.et-found-count {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
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
    padding: 1.25rem;
    margin-bottom: 1rem;
}

.et-post.has-telegram {
    border-color: #3b82f6;
    background: #f8faff;
}

.et-post.no-telegram {
    border-color: #e2e8f0;
    background: #fafafa;
    opacity: 0.6;
}

.et-post-top {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.et-checkbox {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    cursor: pointer;
    flex-shrink: 0;
    accent-color: #3b82f6;
}

.et-post-content {
    flex: 1;
    min-width: 0;
}

.et-post-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.et-post-id {
    background: #f1f5f9;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
}

.et-existing-tg {
    background: #fef3c7;
    color: #92400e;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.et-found-badge {
    background: #dbeafe;
    color: #1d4ed8;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.et-fields {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.et-field-label {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.1rem;
}

.et-field-value {
    color: #1a202c;
    font-size: 0.8rem;
    line-height: 1.4;
    white-space: pre-wrap;
    word-break: break-word;
}

.et-action-preview {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #15803d;
    line-height: 1.5;
}

.et-action-preview b {
    color: #166534;
}

.et-no-result {
    color: #94a3b8;
    font-size: 0.8rem;
    font-style: italic;
    margin-top: 0.25rem;
}

.et-submit-bar {
    position: sticky;
    bottom: 1rem;
    background: white;
    border: 2px solid #3b82f6;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-top: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
}

.et-submit-info {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
}

.et-submit-info span {
    color: #1d4ed8;
    font-weight: 700;
}

.et-btn-execute {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.75rem 2.5rem;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.et-btn-execute:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.4);
}

.et-btn-execute:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

@media (max-width: 768px) {
    .et-container { padding: 0; }
    .et-card { padding: 1.25rem; }
    .et-fields { grid-template-columns: 1fr; }
    .et-submit-bar { flex-direction: column; gap: 0.75rem; }
}
</style>

<div class="et-container">
    <div class="et-card">

        <div class="et-header">
            <h1>Извлечение Telegram</h1>
            @if(count($aiResults) > 0)
                <div class="et-found-count">Найдено: {{ count($aiResults) }} из {{ $posts->count() }}</div>
            @endif
        </div>

        @if(session('success'))
            <div class="et-success">{{ session('success') }}</div>
        @endif

        @if($posts->isEmpty())
            <div class="et-empty">Объявлений нет</div>
        @else
            <form method="POST" action="/extract-telegram-secret/save" id="extractForm">
                @csrf

                @foreach($posts as $post)
                    @php
                        $ai = $aiResults[$post->id] ?? null;
                    @endphp
                    <div class="et-post {{ $ai ? 'has-telegram' : 'no-telegram' }}">
                        <div class="et-post-top">
                            @if($ai)
                                <input type="checkbox" class="et-checkbox" name="items[{{ $post->id }}]" value="{{ $ai['telegram'] }}" checked data-post-id="{{ $post->id }}">
                            @endif
                            <div class="et-post-content">
                                <div class="et-post-header">
                                    <div class="et-post-id">ID: {{ $post->id }}</div>
                                    @if(!empty($post->telegram))
                                        <div class="et-existing-tg">TG: {{ $post->telegram }}</div>
                                    @endif
                                    @if($ai)
                                        <div class="et-found-badge">@{{ $ai['telegram'] }} ({{ $ai['found_in'] }})</div>
                                    @endif
                                </div>

                                <div class="et-fields">
                                    <div>
                                        <div class="et-field-label">Заголовок</div>
                                        <div class="et-field-value">{{ Str::limit($post->title ?? '—', 100) }}</div>
                                    </div>
                                    <div>
                                        <div class="et-field-label">ФИО</div>
                                        <div class="et-field-value">{{ Str::limit($post->fio ?? '—', 100) }}</div>
                                    </div>
                                    <div>
                                        <div class="et-field-label">Описание</div>
                                        <div class="et-field-value">{{ Str::limit($post->discription ?? '—', 150) }}</div>
                                    </div>
                                </div>

                                @if($ai)
                                    <div class="et-action-preview">
                                        @if(empty($post->telegram))
                                            <b>@{{ $ai['telegram'] }}</b> &rarr; запишется в поле Telegram
                                        @else
                                            Поле Telegram уже: <b>{{ $post->telegram }}</b> — не перезапишется
                                        @endif
                                        &bull; ник будет вырезан из текста
                                    </div>
                                @else
                                    <div class="et-no-result">Telegram не найден</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if(count($aiResults) > 0)
                    <div class="et-submit-bar">
                        <div class="et-submit-info">Выбрано: <span id="checkedCount">{{ count($aiResults) }}</span></div>
                        <button type="submit" class="et-btn-execute" id="executeBtn">Выполнить</button>
                    </div>
                @endif
            </form>
        @endif

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.et-checkbox');
    const countEl = document.getElementById('checkedCount');
    const btn = document.getElementById('executeBtn');

    function updateCount() {
        const checked = document.querySelectorAll('.et-checkbox:checked').length;
        if (countEl) countEl.textContent = checked;
        if (btn) btn.disabled = checked === 0;
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
});
</script>

@endsection
