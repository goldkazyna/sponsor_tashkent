@extends('layouts.app')

@section('title', $profile->name.' — анкета | знакомства.KZ')

@section('content')

@php
    $hasPhoto = ! empty($profile->photo);
    $age = $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->age : null;
    $locked = $hasPhoto && ! $isRegistered;
@endphp

<style>
.ps-wrap { max-width: 760px; margin: 24px auto; padding: 0 16px; }
.ps-back { display: inline-block; margin-bottom: 14px; color: #64748b; text-decoration: none; font-size: 0.9rem; }
.ps-back:hover { color: #1a202c; }
.ps-card {
    background: #fff; border-radius: 18px; overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.08); display: grid; grid-template-columns: 300px 1fr;
}
.ps-photo { position: relative; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center; }
.ps-photo img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
.ps-photo .av { width: 46%; height: 46%; fill: rgba(255,255,255,0.9); }
.ps-lock { text-align: center; color: #fff; padding: 16px; }
.ps-lock svg { width: 40px; height: 40px; fill: rgba(255,255,255,0.92); margin-bottom: 10px; }
.ps-lock span { display: block; font-size: 0.9rem; font-weight: 600; line-height: 1.4; }
.ps-body { padding: 1.8rem; }
.ps-name { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.ps-city { display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 0.95rem; margin-bottom: 18px; }
.ps-city svg { width: 16px; height: 16px; fill: #94a3b8; }
.ps-about-title { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; font-weight: 700; margin-bottom: 6px; }
.ps-about { color: #334155; font-size: 1rem; line-height: 1.65; white-space: pre-wrap; margin-bottom: 22px; }
.ps-btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 13px 26px; border-radius: 30px;
    font-weight: 700; font-size: 1rem; text-decoration: none; border: none; cursor: pointer;
}
.ps-btn-primary { background: linear-gradient(135deg, #0088cc, #0077b3); color: #fff; box-shadow: 0 4px 14px rgba(0,136,204,0.35); }
.ps-btn-primary:hover { transform: translateY(-2px); color: #fff; }
.ps-btn-primary svg { width: 20px; height: 20px; fill: #fff; }
.ps-note { color: #64748b; font-size: 0.92rem; }
.ps-note a { color: #0077b3; font-weight: 700; text-decoration: underline; }
@media (max-width: 640px) {
    .ps-card { grid-template-columns: 1fr; }
    .ps-photo { aspect-ratio: 1/1; max-height: 380px; }
}
</style>

<div class="ps-wrap">
    <a href="{{ route('home') }}" class="ps-back">← К анкетам</a>

    <div class="ps-card">
        <div class="ps-photo" style="background: {{ $locked ? 'linear-gradient(135deg,#475569,#1e293b)' : ($profile->sex == 1 ? 'linear-gradient(135deg,#4facfe,#0066ff)' : 'linear-gradient(135deg,#f6a5c0,#f5576c)') }};">
            @if($hasPhoto && $isRegistered)
                <img src="{{ asset($profile->photo) }}" alt="{{ $profile->name }}">
            @elseif($locked)
                <div class="ps-lock">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/></svg>
                    <span>Зарегистрируйтесь,<br>чтобы посмотреть фото</span>
                </div>
            @elseif($profile->sex == 1)
                <svg class="av" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g transform="translate(2.5,0)"><path d="M9.5,5.5A1.5,1.5 0 0,1 8,4A1.5,1.5 0 0,1 9.5,2.5A1.5,1.5 0 0,1 11,4A1.5,1.5 0 0,1 9.5,5.5M9.5,7C11.43,7 13,8.57 13,10.5V16H11V22H8V16H6V10.5C6,8.57 7.57,7 9.5,7Z"/></g></svg>
            @else
                <svg class="av" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.94,8.31C13.62,7.52 12.85,7 12,7C11.15,7 10.39,7.52 10.06,8.31L7,16H9.5V22H14.5V16H17M12,2A2,2 0 0,1 14,4A2,2 0 0,1 12,6A2,2 0 0,1 10,4A2,2 0 0,1 12,2Z"/></svg>
            @endif
        </div>

        <div class="ps-body">
            <h1 class="ps-name">{{ $profile->name }}@if($age), {{ $age }}@endif</h1>
            <div class="ps-city">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/></svg>
                {{ $profile->city_name ?? '—' }}
            </div>

            @if(! empty($profile->about))
                <div class="ps-about-title">О себе</div>
                <div class="ps-about">{{ $profile->about }}</div>
            @endif

            @if($isOwner)
                <a href="{{ route('profile.anketa') }}" class="ps-btn ps-btn-primary">Редактировать мою анкету</a>
            @elseif($isRegistered && $viewerHasProfile)
                <a href="{{ route('profile.messages.chat', $profile->user_id) }}" class="ps-btn ps-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2Z"/></svg>
                    Написать сообщение
                </a>
            @elseif($isRegistered && ! $viewerHasProfile)
                <div class="ps-note">
                    Чтобы написать сообщение — <a href="{{ route('profile.anketa') }}">создайте свою анкету</a>.
                </div>
            @else
                <div class="ps-note">
                    Чтобы написать сообщение — <a href="{{ route('login') }}">войдите</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>.
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
