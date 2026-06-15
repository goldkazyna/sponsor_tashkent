@extends('layouts.app')

@section('title', 'Статистика /for-sale')

@section('content')
<style>
.stats-wrap { max-width: 640px; margin: 2rem auto 3rem; padding: 0 1rem; }
.stats-cards { display: flex; gap: 14px; margin-bottom: 1.8rem; flex-wrap: wrap; }
.stats-card {
    flex: 1; min-width: 150px; background: #fff; border-radius: 14px;
    padding: 1.4rem; box-shadow: 0 4px 16px rgba(0,0,0,0.07); text-align: center;
}
.stats-card .num { font-size: 2.4rem; font-weight: 900; color: #d97706; line-height: 1; }
.stats-card .lbl { margin-top: 6px; font-size: 0.85rem; color: #64748b; font-weight: 600; }
.stats-table {
    width: 100%; background: #fff; border-radius: 14px; overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07); border-collapse: collapse;
}
.stats-table th, .stats-table td { padding: 11px 16px; text-align: left; font-size: 0.95rem; }
.stats-table th { background: #f8fafc; color: #475569; font-weight: 700; }
.stats-table tr:not(:last-child) td { border-bottom: 1px solid #f1f5f9; }
.stats-table td:last-child { text-align: right; font-weight: 700; color: #1a202c; }
.stats-h1 { font-size: 1.4rem; font-weight: 800; color: #1a202c; margin-bottom: 1.2rem; }
</style>

<div class="stats-wrap">
    <div class="stats-h1">📊 Посещения страницы продажи</div>

    <div class="stats-cards">
        <div class="stats-card">
            <div class="num">{{ number_format($total, 0, '.', ' ') }}</div>
            <div class="lbl">Всего просмотров</div>
        </div>
        <div class="stats-card">
            <div class="num">{{ number_format($today, 0, '.', ' ') }}</div>
            <div class="lbl">Сегодня</div>
        </div>
    </div>

    @if(count($days))
    <table class="stats-table">
        <thead><tr><th>Дата</th><th>Просмотров</th></tr></thead>
        <tbody>
            @foreach($days as $date => $count)
            <tr>
                <td>{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</td>
                <td>{{ number_format($count, 0, '.', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color:#64748b;">Пока нет данных.</p>
    @endif
</div>
@endsection
