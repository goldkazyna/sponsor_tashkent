@extends('layouts.app')

@section('title', 'Редактирование объявления')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ time() }}">

<style>
.edit-container {
    max-width: 900px;
    margin: 0 auto;
}

.edit-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.edit-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
}

.edit-header p {
    color: #64748b;
    font-size: 0.9rem;
}

.edit-form {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: #dc2626;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1a202c;
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-select:focus {
    outline: none;
    border-color: #1a202c;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #f1f5f9;
}

.btn-save {
    flex: 1;
    padding: 0.9rem;
    background: #1a202c;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26, 32, 44, 0.3);
}

.btn-cancel {
    padding: 0.9rem 2rem;
    background: #f8fafc;
    color: #64748b;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-cancel:hover {
    background: white;
    border-color: #cbd5e1;
    color: #1a202c;
    text-decoration: none;
}

.photos-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.photo-item {
    position: relative;
    width: 100%;
    padding-bottom: 0 !important;
    height: 120px !important;
    border-radius: 10px;
    overflow: hidden;
    background: #f1f5f9;
    border: 2px solid #e2e8f0;
}

.photo-item img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.photo-delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 28px;
    height: 28px;
    background: rgba(220, 38, 38, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
}

.photo-delete-btn:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.photo-delete-btn svg {
    width: 16px;
    height: 16px;
    fill: white;
}

/* Галерея загрузки новых фото */
.photo-gallery {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-top: 10px;
}

.photo-slot {
    position: relative;
    width: 100%;
    padding-bottom: 100%;
    border: 2px dashed #ddd;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
    overflow: hidden;
}

.photo-slot:hover {
    border-color: #27ae60;
    background: #e8f5e9;
}

.photo-slot.has-image {
    border-style: solid;
    border-color: #27ae60;
}

.photo-upload-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    background: #27ae60;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.photo-slot.has-image .photo-upload-btn {
    display: none;
}

.photo-preview {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-remove-new {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 25px;
    height: 25px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: none;
    z-index: 10;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transition: all 0.3s;
}

.photo-slot.has-image .photo-remove-new {
    display: block;
}

.photo-remove-new:hover {
    background: #c0392b;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .edit-header {
        padding: 1.2rem;
    }

    .edit-header h1 {
        font-size: 1.4rem;
    }

    .edit-form {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel {
        order: 2;
    }

    .photo-gallery {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

<div class="edit-container">
    
    <!-- Шапка -->
    <div class="edit-header">
        <h1>Редактирование объявления</h1>
        <p>Измените информацию в объявлении</p>
    </div>

    <!-- Форма редактирования -->
    <form action="{{ route('profile.post.update', $post->id) }}" method="POST" class="edit-form">
        @csrf

        <div class="form-group">
            <label class="form-label required">Заголовок объявления</label>
            <input type="text" name="title" class="form-input" value="{{ $post->title }}" required>
        </div>

        <div class="form-group">
            <label class="form-label required">ФИО</label>
            <input type="text" name="fio" class="form-input" value="{{ $post->fio }}" required>
        </div>

        <div class="form-group">
            <label class="form-label required">Описание</label>
            <textarea name="discription" class="form-input form-textarea" required>{{ $post->discription }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label required">Город</label>
            <select name="city" class="form-select" required>
                <option value="">Выберите город</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ $post->city == $city->id ? 'selected' : '' }}>
                        {{ $city->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label required">Телефон</label>
            <input type="text" name="phone" class="form-input" value="{{ $post->phone }}" placeholder="+7 700 123 45 67" required>
        </div>

        <div class="form-group">
            <label class="form-label">Telegram</label>
            <input type="text" name="telegram" class="form-input" value="{{ $post->telegram ?? '' }}" placeholder="@username">
        </div>

        <div class="form-group">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="whats" class="form-input" value="{{ $post->whats ?? '' }}" placeholder="+7 700 123 45 67">
        </div>

        @if(count($photos) > 0)
        <div class="form-group">
            <label class="form-label">Загруженные фотографии ({{ count($photos) }})</label>
            <div class="photos-preview">
                @foreach($photos as $photo)
                    <div class="photo-item" id="photo-{{ $photo->id }}">
                        <img src="{{ asset('/' . $photo->thumb_webp) }}" alt="Фото">
                        <button type="button" class="photo-delete-btn" onclick="deletePhoto({{ $photo->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="form-group">
            <label class="form-label">{{ count($photos) > 0 ? 'Добавить ещё фото' : 'Добавьте фото' }}</label>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Вы можете добавить до {{ 10 - count($photos) }} фотографий. Форматы: JPG, PNG</p>
            
            <div class="photo-gallery" id="photoGallery">
                <!-- Слоты для новых фото -->
                @for($i = 0; $i < (10 - count($photos)); $i++)
                <div class="photo-slot" id="photoSlot{{ $i }}" onclick="document.getElementById('photoInput{{ $i }}').click()">
                    <input type="file" 
                           id="photoInput{{ $i }}" 
                           accept="image/jpeg,image/png,image/jpg" 
                           onchange="handlePhotoUpload({{ $i }}, this)"
                           style="display: none;">
                    <div class="photo-upload-btn">+</div>
                    <button type="button" class="photo-remove-new" onclick="event.stopPropagation(); removeNewPhoto({{ $i }})">&times;</button>
                </div>
                @endfor
            </div>
            
            <!-- Скрытые поля для передачи base64 изображений -->
            <div id="photoDataContainer"></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: currentColor; display: inline-block; vertical-align: middle; margin-right: 6px;">
                    <path d="M15,9H5V5H15M12,19A3,3 0 0,1 9,16A3,3 0 0,1 12,13A3,3 0 0,1 15,16A3,3 0 0,1 12,19M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3Z"/>
                </svg>
                Сохранить изменения
            </button>
            <a href="{{ route('profile.posts') }}" class="btn-cancel">Отмена</a>
        </div>

    </form>

</div>

<script>
// Функция удаления существующего фото
function deletePhoto(photoId) {
    if (!confirm('Удалить эту фотографию?')) {
        return;
    }

    fetch('/profile/photo/delete/' + photoId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const photoElement = document.getElementById('photo-' + photoId);
            if (photoElement) {
                photoElement.style.opacity = '0';
                setTimeout(() => {
                    photoElement.remove();
                    
                    // Обновляем счётчик фото
                    const photosCount = document.querySelectorAll('.photo-item').length;
                    const label = document.querySelector('.photos-preview').previousElementSibling;
                    if (label) {
                        label.textContent = 'Загруженные фотографии (' + photosCount + ')';
                    }
                    
                    // Если фото не осталось, скрываем блок
                    if (photosCount === 0) {
                        const formGroup = document.querySelector('.photos-preview').closest('.form-group');
                        if (formGroup) {
                            formGroup.remove();
                        }
                    }
                    
                    // Перезагружаем страницу чтобы показать новые слоты
                    location.reload();
                }, 300);
            }
        } else {
            alert('Ошибка при удалении: ' + (data.error || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Ошибка при удалении фотографии');
    });
}

// Массив для новых загруженных фото
let uploadedPhotos = [];

// Функция загрузки нового фото
function handlePhotoUpload(index, input) {
    const file = input.files[0];
    
    if (!file) return;
    
    // Проверка типа файла
    if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
        alert('Пожалуйста, выберите файл формата JPG или PNG');
        input.value = '';
        return;
    }
    
    // Проверка размера (макс 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Размер файла не должен превышать 5MB');
        input.value = '';
        return;
    }
    
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const photoSlot = document.getElementById('photoSlot' + index);
        
        // Создаем превью
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'photo-preview';
        
        // Удаляем старое превью если есть
        const oldPreview = photoSlot.querySelector('.photo-preview');
        if (oldPreview) {
            oldPreview.remove();
        }
        
        // Добавляем новое превью
        photoSlot.appendChild(img);
        photoSlot.classList.add('has-image');
        
        // Сохраняем данные фото
        uploadedPhotos[index] = {
            file: file,
            dataUrl: e.target.result
        };
        
        // Добавляем скрытое поле с данными
        updatePhotoDataFields();
    };
    
    reader.readAsDataURL(file);
}

// Функция удаления нового фото (до сохранения)
function removeNewPhoto(index) {
    const photoSlot = document.getElementById('photoSlot' + index);
    const photoInput = document.getElementById('photoInput' + index);
    const preview = photoSlot.querySelector('.photo-preview');
    
    if (preview) {
        preview.remove();
    }
    
    photoSlot.classList.remove('has-image');
    photoInput.value = '';
    delete uploadedPhotos[index];
    
    updatePhotoDataFields();
}

// Обновление скрытых полей с данными фото
function updatePhotoDataFields() {
    const container = document.getElementById('photoDataContainer');
    container.innerHTML = '';
    
    uploadedPhotos.forEach((photo, index) => {
        if (photo) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photos[]';
            input.value = photo.dataUrl;
            container.appendChild(input);
        }
    });
}
</script>

@endsection