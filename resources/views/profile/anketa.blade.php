@extends('layouts.app')

@section('title', 'Моя анкета')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.anketa-container { max-width: 720px; margin: 0 auto; padding: 0 1rem; }
.anketa-card {
    background: #fff; border-radius: 16px; padding: 2rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08); margin-bottom: 1.5rem;
}
.anketa-card h1 { font-size: 1.5rem; font-weight: 800; color: #1a202c; margin: 0 0 0.4rem; }
.anketa-card .sub { color: #64748b; font-size: 0.92rem; margin: 0 0 1.5rem; }
.form-group { margin-bottom: 1.25rem; }
.form-label { display: block; font-weight: 600; color: #1a202c; margin-bottom: 0.5rem; font-size: 0.9rem; }
.form-label .req { color: #ef4444; }
.form-input, .form-select, .form-textarea {
    width: 100%; padding: 0.8rem 1rem; border: 2px solid #e2e8f0; border-radius: 10px;
    font-size: 0.95rem; font-family: inherit; transition: border-color 0.2s;
}
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #f59e0b; }
.form-textarea { resize: vertical; min-height: 110px; }
.photo-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.anketa-photo {
    width: 110px; height: 140px; border-radius: 12px; object-fit: cover;
    background: #f1f5f9; border: 2px solid #e2e8f0; flex-shrink: 0;
}
.photo-preview-empty {
    width: 110px; height: 140px; border-radius: 12px; background: #f1f5f9;
    border: 2px dashed #cbd5e1; display: flex; align-items: center; justify-content: center;
    color: #94a3b8; flex-shrink: 0;
}
.photo-preview-empty svg { width: 40px; height: 40px; fill: #cbd5e1; }
.btn-save {
    width: 100%; padding: 1rem; background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem;
    cursor: pointer; transition: transform 0.15s, box-shadow 0.2s;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245,158,11,0.35); }
.alert-success {
    background: #d1fae5; border: 2px solid #6ee7b7; color: #065f46;
    padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;
}
.alert-errors {
    background: #fee2e2; border: 2px solid #fca5a5; color: #991b1b;
    padding: 1rem 1.2rem; border-radius: 10px; margin-bottom: 1.5rem;
}
.alert-errors ul { margin: 0; padding-left: 1.1rem; }
.back-link { display: inline-block; margin-bottom: 1.2rem; color: #64748b; text-decoration: none; font-size: 0.9rem; }
.back-link:hover { color: #1a202c; }
</style>

<div class="anketa-container">
    <a href="{{ route('profile.posts') }}" class="back-link">← В кабинет</a>

    <div class="anketa-card">
        <h1>Моя анкета</h1>
        <p class="sub">Заполните анкету — она сразу появится на сайте знакомств.</p>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.anketa.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Фото</label>
                <div class="photo-row">
                    <div class="photo-preview-empty" id="photoPreviewEmpty" @if($profile && $profile->photo) style="display:none;" @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,12A5,5 0 0,0 17,7A5,5 0 0,0 12,2A5,5 0 0,0 7,7A5,5 0 0,0 12,12M12,14C8.67,14 2,15.67 2,19V22H22V19C22,15.67 15.33,14 12,14Z"/></svg>
                    </div>
                    <img class="anketa-photo" id="photoPreview" alt="Фото"
                         @if($profile && $profile->photo) src="{{ asset($profile->photo) }}?v={{ time() }}" @else style="display:none;" @endif>
                    <input type="file" name="photo" accept="image/*" class="form-input" id="photoInput" style="max-width:320px;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Имя <span class="req">*</span></label>
                <input type="text" name="name" class="form-input" maxlength="100" required
                       value="{{ old('name', $profile->name ?? ($user->fio ?? '')) }}" placeholder="Как вас зовут">
            </div>

            <div class="form-group">
                <label class="form-label">Дата рождения <span class="req">*</span></label>
                <input type="date" name="birthdate" class="form-input" required
                       value="{{ old('birthdate', $profile->birthdate ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Город <span class="req">*</span></label>
                <select name="city_id" class="form-select" required>
                    <option value="">— выберите город —</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ (string) old('city_id', $profile->city_id ?? '') === (string) $city->id ? 'selected' : '' }}>
                            {{ $city->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">О себе</label>
                <textarea name="about" class="form-textarea" maxlength="2000" placeholder="Расскажите о себе...">{{ old('about', $profile->about ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn-save">Сохранить анкету</button>
        </form>
    </div>
</div>

<script>
// Превью выбранного фото
document.getElementById('photoInput').addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (!file) return;
    var preview = document.getElementById('photoPreview');
    var empty = document.getElementById('photoPreviewEmpty');
    var reader = new FileReader();
    reader.onload = function (ev) {
        preview.src = ev.target.result;
        preview.style.display = 'block';
        if (empty) empty.style.display = 'none';
    };
    reader.readAsDataURL(file);
});
</script>

@endsection
