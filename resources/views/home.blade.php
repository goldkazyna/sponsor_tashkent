@extends('layouts.app')

@section('title', 'Знакомства в Казахстане — анкеты девушек и мужчин | знакомства.KZ')

@section('meta_description', 'Сайт знакомств: анкеты девушек и мужчин из Алматы, Астаны, Шымкента и других городов Казахстана. Регистрируйтесь и знакомьтесь.')

@section('content')

{{-- Анкеты сайта знакомств (данные из таблицы profiles, передаются контроллером) --}}
@php
$isRegistered = (bool) session('user_id');
$visibleProfiles = $profiles ?? collect();
@endphp

<style>
.profiles-wrap { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
.profiles-head { margin-bottom: 20px; }
.profiles-head h1 { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.profiles-head p { color: #64748b; font-size: 0.95rem; margin: 0; }
.profiles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 18px;
}
.profile-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    border: 1px solid #f1f5f9;
}
.profile-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.14); }
.profile-photo {
    position: relative;
    aspect-ratio: 3 / 4;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-photo .avatar-icon { width: 50%; height: 50%; fill: rgba(255,255,255,0.9); }
.lock-overlay { text-align: center; color: #fff; padding: 12px; }
.lock-overlay svg { width: 34px; height: 34px; fill: rgba(255,255,255,0.92); margin-bottom: 8px; }
.lock-overlay span { display: block; font-size: 0.8rem; line-height: 1.35; font-weight: 600; opacity: 0.96; }
.profile-info { padding: 12px 14px 14px; }
.profile-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.2;
}
.profile-city {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #64748b;
    font-size: 0.88rem;
}
.profile-city svg { width: 14px; height: 14px; fill: #94a3b8; flex-shrink: 0; }
@media (max-width: 480px) {
    .profiles-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .profile-name { font-size: 0.95rem; }
}
</style>

<div class="profiles-wrap">
    <div class="profiles-head">
        <h1>Анкеты</h1>
        <p>Тестовый дизайн — примерные данные</p>
    </div>

    @include('partials.city-filter')

    <div class="profiles-grid">
        @forelse($visibleProfiles as $p)
        @php
            $hasPhoto = ! empty($p->photo);
            $locked = $hasPhoto && ! $isRegistered;
            $age = $p->birthdate ? \Carbon\Carbon::parse($p->birthdate)->age : null;
            if ($locked) {
                $bg = 'linear-gradient(135deg,#475569,#1e293b)';
            } elseif ($p->sex == 1) {
                $bg = 'linear-gradient(135deg,#4facfe,#0066ff)';
            } else {
                $bg = 'linear-gradient(135deg,#f6a5c0,#f5576c)';
            }
        @endphp
        <a href="{{ route('profile.show', $p->id) }}" class="profile-card" style="text-decoration:none; color:inherit;">
            <div class="profile-photo {{ $locked ? 'locked' : '' }}" style="background: {{ $bg }};">
                @if($hasPhoto && $isRegistered)
                    <img src="{{ asset($p->photo) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                @elseif($locked)
                    <div class="lock-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                        <span>Зарегистрируйтесь,<br>чтобы посмотреть фото</span>
                    </div>
                @elseif($p->sex == 1)
                    {{-- Аватар мужчины --}}
                    <svg class="avatar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g transform="translate(2.5,0)"><path d="M9.5,5.5A1.5,1.5 0 0,1 8,4A1.5,1.5 0 0,1 9.5,2.5A1.5,1.5 0 0,1 11,4A1.5,1.5 0 0,1 9.5,5.5M9.5,7C11.43,7 13,8.57 13,10.5V16H11V22H8V16H6V10.5C6,8.57 7.57,7 9.5,7Z"/></g></svg>
                @else
                    {{-- Аватар женщины --}}
                    <svg class="avatar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.94,8.31C13.62,7.52 12.85,7 12,7C11.15,7 10.39,7.52 10.06,8.31L7,16H9.5V22H14.5V16H17M12,2A2,2 0 0,1 14,4A2,2 0 0,1 12,6A2,2 0 0,1 10,4A2,2 0 0,1 12,2Z"/></svg>
                @endif
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ $p->name }}@if($age), {{ $age }}@endif</div>
                <div class="profile-city">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                    {{ $p->city_name ?? '—' }}
                </div>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1; text-align:center; color:#64748b; padding:40px 0;">Анкеты не найдены.</div>
        @endforelse
    </div>
</div>

@endsection