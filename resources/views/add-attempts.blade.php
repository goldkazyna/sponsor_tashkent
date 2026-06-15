@extends('layouts.app')

@section('title', 'Попытки добавления объявлений')

@section('content')
<style>
.att-wrap { max-width: 1100px; margin: 2rem auto 3rem; padding: 0 1rem; }
.att-h1 { font-size: 1.4rem; font-weight: 800; color: #1a202c; margin-bottom: 1.2rem; }
.att-cards { display: flex; gap: 14px; margin-bottom: 1.5rem; flex-wrap: wrap; }
.att-card {
    flex: 1; min-width: 140px; background: #fff; border-radius: 14px;
    padding: 1.2rem; box-shadow: 0 4px 16px rgba(0,0,0,0.07); text-align: center;
}
.att-card .num { font-size: 2rem; font-weight: 900; line-height: 1; }
.att-card .lbl { margin-top: 6px; font-size: 0.85rem; color: #64748b; font-weight: 600; }
.att-list { display: flex; flex-direction: column; gap: 10px; }
.att-item {
    background: #fff; border-radius: 12px; padding: 14px 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); border-left: 5px solid #22c55e;
}
.att-item.blocked { border-left-color: #ef4444; }
.att-top { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 6px; }
.att-badge {
    font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.att-badge.ok { background: #dcfce7; color: #166534; }
.att-badge.no { background: #fee2e2; color: #991b1b; }
.att-title { font-weight: 700; color: #1a202c; font-size: 1rem; }
.att-meta { font-size: 0.78rem; color: #94a3b8; }
.att-desc { color: #475569; font-size: 0.9rem; line-height: 1.5; margin-top: 4px; white-space: pre-wrap; word-break: break-word; }
.att-reason {
    margin-top: 8px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;
    border-radius: 8px; padding: 8px 10px; font-size: 0.85rem;
}
.att-empty { color: #64748b; text-align: center; padding: 3rem 0; }
</style>

<div class="att-wrap">
    <div class="att-h1">📝 Попытки добавления объявлений</div>

    <div class="att-cards">
        <div class="att-card">
            <div class="num" style="color:#1a202c;">{{ number_format($total, 0, '.', ' ') }}</div>
            <div class="lbl">Всего попыток</div>
        </div>
        <div class="att-card">
            <div class="num" style="color:#ef4444;">{{ number_format($blocked, 0, '.', ' ') }}</div>
            <div class="lbl">Заблокировано</div>
        </div>
        <div class="att-card">
            <div class="num" style="color:#22c55e;">{{ number_format($total - $blocked, 0, '.', ' ') }}</div>
            <div class="lbl">Пропущено</div>
        </div>
    </div>

    @if(count($entries))
    <div class="att-list">
        @foreach($entries as $e)
        <div class="att-item {{ empty($e['allowed']) ? 'blocked' : '' }}">
            <div class="att-top">
                @if(empty($e['allowed']))
                    <span class="att-badge no">Заблокировано</span>
                @else
                    <span class="att-badge ok">Пропущено</span>
                @endif
                <span class="att-title">{{ $e['title'] ?? '' }}</span>
                <span class="att-meta">
                    {{ $e['at'] ?? '' }}
                    @if(!empty($e['city'])) · {{ $e['city'] }} @endif
                    @if(!empty($e['fio'])) · {{ $e['fio'] }} @endif
                    @if(!empty($e['email'])) · {{ $e['email'] }} @endif
                    @if(!empty($e['ip'])) · {{ $e['ip'] }} @endif
                </span>
            </div>
            @if(!empty($e['description']))
                <div class="att-desc">{{ $e['description'] }}</div>
            @endif
            @if(empty($e['allowed']) && !empty($e['reason']))
                <div class="att-reason">⛔ {{ $e['reason'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @else
        <div class="att-empty">Пока нет ни одной попытки.</div>
    @endif
</div>
@endsection
