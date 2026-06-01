@extends('layouts.app')

@section('title', 'Поиск 16/17/девственниц')

@section('content')

<style>
.sm-container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
.sm-card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
.sm-card h1 { font-size: 1.5rem; font-weight: 700; color: #1a202c; margin: 0 0 0.5rem; }
.sm-sub { color: #64748b; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem; }

.sm-progress-bar { background: #e2e8f0; border-radius: 999px; height: 14px; overflow: hidden; margin: 0.75rem 0; }
.sm-progress-fill { background: linear-gradient(135deg, #3b82f6, #2563eb); height: 100%; transition: width 0.3s ease; }
.sm-stats { display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.95rem; color: #475569; margin-bottom: 1.25rem; }
.sm-stats b { color: #1a202c; }

.sm-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
.sm-btn { padding: 0.85rem 1.6rem; border: none; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s ease; color: white; background: linear-gradient(135deg, #3b82f6, #2563eb); }
.sm-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }
.sm-btn-secondary { background: #f1f5f9; color: #475569; border: 2px solid #e2e8f0; font-weight: 600; font-size: 0.85rem; padding: 0.6rem 1.1rem; }
.sm-btn-secondary:hover { background: #e2e8f0; box-shadow: none; transform: none; }

.sm-success { background: #d1fae5; border: 2px solid #6ee7b7; color: #065f46; padding: 0.9rem 1.1rem; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600; }
.sm-warn { background: #fef2f2; border: 2px solid #fca5a5; color: #991b1b; padding: 0.9rem 1.1rem; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600; }

.sm-found-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
.sm-found-head h2 { font-size: 1.2rem; font-weight: 700; color: #1a202c; margin: 0; }
.sm-count-badge { background: #fee2e2; color: #991b1b; padding: 0.4rem 0.9rem; border-radius: 999px; font-weight: 700; font-size: 0.9rem; }

.sm-item { border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 0.85rem; }
.sm-item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
.sm-item-id { font-weight: 800; color: #1a202c; font-size: 1.05rem; }
.sm-item-title { color: #334155; font-size: 0.95rem; margin: 0.35rem 0; }
.sm-reason { display: inline-block; padding: 0.2rem 0.7rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; margin-right: 0.4rem; }
.sm-reason-age { background: #fee2e2; color: #b91c1c; }
.sm-reason-virgin { background: #f3e8ff; color: #7e22ce; }
.sm-fragment { background: #fffbeb; border-left: 3px solid #f59e0b; padding: 0.5rem 0.75rem; margin-top: 0.5rem; color: #92400e; font-size: 0.88rem; border-radius: 0 6px 6px 0; }
.sm-item-btns { display: flex; gap: 0.5rem; flex-shrink: 0; }
.sm-del-btn { padding: 0.55rem 1.1rem; border: none; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; color: white; background: #ef4444; }
.sm-del-btn:hover { background: #dc2626; }
.sm-dismiss-btn { padding: 0.55rem 0.9rem; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; color: #64748b; background: white; }
.sm-dismiss-btn:hover { background: #f8fafc; }

.sm-empty { text-align: center; color: #94a3b8; padding: 2rem 1rem; font-size: 0.95rem; }
</style>

<div class="sm-container">

    @if(session('success'))
        <div class="sm-success">{{ session('success') }}</div>
    @endif

    @if(!$apiKeySet)
        <div class="sm-warn">⚠️ ANTHROPIC_API_KEY не задан в .env — сканирование работать не будет.</div>
    @endif

    @php
        $pct = $total > 0 ? min(100, round($done / $total * 100, 1)) : 0;
        $remaining = max(0, $total - $done);
    @endphp

    <div class="sm-card">
        <h1>🔎 Поиск: 16/17 лет и младше + девственницы</h1>
        <p class="sm-sub">
            AI проверяет объявления и помечает те, где упоминается возраст <b>16, 17 или младше</b>,
            либо <b>девственность</b>. Ничего не удаляется автоматически — вы решаете сами.
            Сканирование идёт от новых объявлений к старым, по 50 за раз.
        </p>

        <div class="sm-progress-bar">
            <div class="sm-progress-fill" style="width: {{ $pct }}%;"></div>
        </div>
        <div class="sm-stats">
            <span>Просканировано: <b>{{ number_format($done, 0, '.', ' ') }}</b> из <b>{{ number_format($total, 0, '.', ' ') }}</b> ({{ $pct }}%)</span>
            <span>Осталось: <b>{{ number_format($remaining, 0, '.', ' ') }}</b></span>
        </div>

        <div class="sm-actions">
            <form action="{{ url('/scan-minors-secret/scan') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="sm-btn" {{ $apiKeySet ? '' : 'disabled' }}
                        onclick="this.disabled=true;this.innerHTML='⏳ Сканирую 50…';this.form.submit();">
                    ▶ Сканировать следующие 50
                </button>
            </form>

            <form action="{{ url('/scan-minors-secret/reset') }}" method="POST" style="margin:0;"
                  onsubmit="return confirm('Сбросить позицию? Сканирование начнётся заново с самых новых объявлений. Найденный список останется.');">
                @csrf
                <button type="submit" class="sm-btn-secondary">↺ Сбросить позицию</button>
            </form>

            <form action="{{ url('/scan-minors-secret/reset') }}" method="POST" style="margin:0;"
                  onsubmit="return confirm('Сбросить позицию И очистить весь найденный список?');">
                @csrf
                <input type="hidden" name="clear_results" value="1">
                <button type="submit" class="sm-btn-secondary">🗑 Сбросить + очистить список</button>
            </form>
        </div>
    </div>

    <div class="sm-card">
        <div class="sm-found-head">
            <h2>Найденные объявления</h2>
            <span class="sm-count-badge">Всего: {{ count($results) }}</span>
        </div>

        @forelse($results as $r)
            <div class="sm-item">
                <div class="sm-item-top">
                    <div style="flex:1; min-width:200px;">
                        <div class="sm-item-id">ID #{{ $r['id'] }}</div>
                        <div class="sm-item-title">{{ $r['title'] !== '' ? $r['title'] : '(без заголовка)' }}</div>
                        <div>
                            @if(str_contains($r['reason'], 'age') || str_contains($r['reason'], 'both'))
                                <span class="sm-reason sm-reason-age">🔴 возраст</span>
                            @endif
                            @if(str_contains($r['reason'], 'virgin') || str_contains($r['reason'], 'both'))
                                <span class="sm-reason sm-reason-virgin">🟣 девственница</span>
                            @endif
                        </div>
                        @if(!empty($r['fragment']))
                            <div class="sm-fragment">«{{ $r['fragment'] }}»</div>
                        @endif
                    </div>
                    <div class="sm-item-btns">
                        <form action="{{ url('/scan-minors-secret/dismiss') }}" method="POST" style="margin:0;">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $r['id'] }}">
                            <button type="submit" class="sm-dismiss-btn" title="Убрать из списка, не удаляя объявление">Не то</button>
                        </form>
                        <form action="{{ url('/scan-minors-secret/delete') }}" method="POST" style="margin:0;"
                              onsubmit="return confirm('Удалить объявление #{{ $r['id'] }}? (мягкое удаление, del=1)');">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $r['id'] }}">
                            <button type="submit" class="sm-del-btn">🗑 Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm-empty">Пока ничего не найдено. Нажмите «Сканировать следующие 50».</div>
        @endforelse
    </div>

</div>

@endsection
