@extends('layouts.app')

@section('title', 'Добавить объявление')

@section('content')
<div style=" margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px; color: #222;">Добавить объявление</h2>

	<!-- Информационный блок с правилами -->
	<div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
		<h3 style="color: #856404; margin-bottom: 15px; font-size: 18px;">📋 Важная информация</h3>
		
		<div style="background: #ffe69c; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
			<p style="margin: 0; color: #856404; font-weight: 500;">
				Мужчинам для подачи объявления необходимо приобрести статус проверенного пользователя.
				Девушки могут подавать объявление бесплатно.
			</p>
		</div>
		
		<div style="font-size: 13px; color: #666; line-height: 1.8;">
			<p style="margin-bottom: 10px;">
				<strong>1.</strong> Указывайте верный Email. Ваш email опубликован не будет, но на него будут приходить письма с сайта. Всё конфиденциально.
			</p>
			<p style="margin-bottom: 10px;">
				<strong>2.</strong> WhatsApp. Если вы хотите, чтобы вам писали в WhatsApp, то указывайте его в поле "номер телефона". Он будет опубликован на сайте.
			</p>
			<p style="margin-bottom: 10px;">
				<strong>3.</strong> Чтобы удалить добавленное объявление, зарегистрируйтесь на сайте, указав email оставленного объявления. После этого вы сможете удалить объявление в личном кабинете.
			</p>
			<p style="margin: 0;">
				<strong>4.</strong> Обращение к девушкам!!! Если вы хотите найти проверенного спонсора, указывайте в объявлении, что ищете спонсора со статусом проверенного спонсора на нашем сайте. Эти спонсоры прошли проверку на платёжеспособность.
			</p>
		</div>
	</div>


    
    @if($errors->any())
        <div style="background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="list-style: none; padding: 0; margin: 0; color: #c00;">
                @foreach($errors->all() as $error)
                    <li style="margin-bottom: 5px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('post.store') }}">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Заголовок объявления: *</label>
            <input type="text" name="title" value="{{ old('title') }}" required 
                   placeholder="Например: Ищу партнера для серьезных отношений"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">ФИО или Имя: *</label>
            <input type="text" name="fio" value="{{ old('fio', $user->fio) }}" required 
                   placeholder="Ваше имя"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Телефон:</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       placeholder="+77001234567"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Город: *</label>
                <select name="city" required 
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">Выберите город</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city') == $city->id ? 'selected' : '' }}>
                            {{ $city->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">WhatsApp:</label>
                <input type="text" name="whats" value="{{ old('whats') }}" 
                       placeholder="+77001234567"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Telegram:</label>
                <input type="text" name="telegram" value="{{ old('telegram') }}" 
                       placeholder="@username"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Описание: *</label>
            <textarea name="discription" rows="8" required
                      placeholder="Расскажите о себе и о том, кого ищете..."
                      style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('discription') }}</textarea>
            
        </div>

        <div style="margin-bottom: 30px;">
			<label style="display: block; margin-bottom: 12px; font-weight: 500; font-size: 16px;">Добавьте фото:</label>
			<p style="font-size: 13px; color: #666; margin-bottom: 15px;">Вы можете добавить до 10 фотографий. Форматы: JPG, PNG</p>
			
			<div class="photo-gallery" id="photoGallery">
				<!-- Слоты для фото (10 штук) -->
				@for($i = 0; $i < 10; $i++)
				<div class="photo-item" id="photoSlot{{ $i }}" onclick="document.getElementById('photoInput{{ $i }}').click()">
					<input type="file" 
						   id="photoInput{{ $i }}" 
						   accept="image/jpeg,image/png,image/jpg" 
						   onchange="handlePhotoUpload({{ $i }}, this)"
						   style="display: none;">
					<div class="photo-upload-btn">+</div>
					<button type="button" class="photo-remove" onclick="event.stopPropagation(); removePhoto({{ $i }})">&times;</button>
				</div>
				@endfor
			</div>
			
			<!-- Скрытые поля для передачи base64 изображений -->
			<div id="photoDataContainer"></div>
		</div>

		<div style="margin-bottom: 30px;">
			<label style="display: flex; align-items: center; cursor: pointer;">
				<input type="checkbox" name="photo_view" value="1" {{ old('photo_view') ? 'checked' : '' }} 
					   style="margin-right: 8px; width: 18px; height: 18px;">
				<span>Показывать фото в объявлении</span>
			</label>
		</div>

        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 0; color: #666; font-size: 14px;">
                <strong>Вы добавляете объявление как:</strong> 
                {{ $user->sex == 1 ? '👨 Мужчина (Спонсор)' : '👩 Женщина (Содержанка)' }}
            </p>
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 16px;">
            Опубликовать объявление
        </button>
    </form>
</div>
@if(!empty($nextAt))
<div id="limitModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:16px;padding:2rem;max-width:450px;width:100%;position:relative;text-align:center;">
        <div style="font-size:3rem;margin-bottom:1rem;">⏳</div>
        <div style="font-size:1.3rem;font-weight:700;color:#1a202c;margin-bottom:1rem;">Лимит объявлений</div>
        <div style="color:#475569;font-size:0.95rem;line-height:1.7;margin-bottom:1.5rem;">
            Вы можете подавать только <strong>1 объявление в сутки</strong>.<br>
            Следующее объявление можно будет подать через:
        </div>
        <div id="countdown" style="font-size:1.5rem;font-weight:700;color:#f97316;margin-bottom:1.5rem;"></div>
        <a href="/" style="display:inline-block;padding:0.75rem 2rem;background:#10b981;color:white;border-radius:10px;font-weight:600;text-decoration:none;">На главную</a>
    </div>
</div>
<script>
(function(){
    var target = new Date("{{ $nextAt->toIso8601String() }}").getTime();
    function update(){
        var diff = target - Date.now();
        if(diff <= 0){ document.getElementById('countdown').textContent = 'Уже можно!'; location.reload(); return; }
        var h = Math.floor(diff/3600000), m = Math.floor((diff%3600000)/60000), s = Math.floor((diff%60000)/1000);
        document.getElementById('countdown').textContent = (h > 0 ? h + ' ч. ' : '') + m + ' мин. ' + s + ' сек.';
    }
    update();
    setInterval(update, 1000);
})();
</script>
@endif

@endsection
<script>
let uploadedPhotos = [];

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

function removePhoto(index) {
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