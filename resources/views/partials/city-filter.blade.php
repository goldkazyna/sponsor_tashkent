<!-- ВАРИАНТ 2 - УЛУЧШЕННЫЙ: С РЕАЛЬНОЙ ФИЛЬТРАЦИЕЙ -->

<?php
// Получаем все города из базы, сортировка по id ASC (популярные первые)
$allCities = DB::table('city')->orderBy('id', 'asc')->get();

// Популярные города (первые 7)
$popularCities = $allCities->take(7);

// Получаем выбранный город из параметра (по умолчанию 'all')
$selectedCity = request()->get('city', 'all');
?>

<div class="city-filter-v2-improved">
    <div class="filter-container-v2-improved">
        
        <!-- Десктопная версия -->
        <div class="desktop-filter-v2">
            <div class="city-buttons-v2">
                <button class="city-btn-v2 {{ $selectedCity === 'all' ? 'active' : '' }}" data-city="all">
                    Все города
                </button>
                
                @foreach($popularCities as $city)
                <button class="city-btn-v2 {{ $selectedCity == $city->id ? 'active' : '' }}" data-city="{{ $city->id }}">
                    {{ $city->title }}
                </button>
                @endforeach
                
                <!-- Кнопка "Ещё города" -->
                <button class="city-btn-v2 more-cities-btn-v2" onclick="openModalV2Improved()">
                    Ещё города
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="arrow-icon-v2">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Мобильная версия -->
        <div class="mobile-filter-v2">
            <button class="mobile-search-btn-v2" onclick="openModalV2Improved()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                </svg>
                Поиск по городам
                @if($selectedCity !== 'all')
                    <span class="selected-city-badge-v2">{{ $selectedCity }}</span>
                @endif
            </button>
        </div>
        
    </div>
</div>

<!-- Модальное окно -->
<div class="city-modal-v2-improved" id="cityModalV2Improved" onclick="closeModalV2Improved(event)">
    <div class="modal-content-v2-improved" onclick="event.stopPropagation()">
        <div class="modal-header-v2-improved">
            <h3>Выберите город</h3>
            <button class="close-btn-v2-improved" onclick="closeModalV2Improved()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                </svg>
            </button>
        </div>
        
        <div class="modal-body-v2-improved">
            <div class="modal-grid-v2-improved">
                <button class="modal-city-btn-v2-improved {{ $selectedCity === 'all' ? 'active' : '' }}" data-city="all" onclick="selectCityV2Improved('all')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                    </svg>
                    Все города
                </button>
                
                @foreach($allCities as $city)
                <button class="modal-city-btn-v2-improved {{ $selectedCity == $city->id ? 'active' : '' }}" data-city="{{ $city->id }}" onclick="selectCityV2Improved('{{ $city->id }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                    </svg>
                    {{ $city->title }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* ВАРИАНТ 2 - УЛУЧШЕННЫЙ */
.city-filter-v2-improved {
    max-width: 1200px;
    margin: 0 auto 30px;
}

.filter-container-v2-improved {
    background: white;
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    border: 2px solid #f1f5f9;
}

/* Мобильная версия скрыта на десктопе */
.mobile-filter-v2 {
    display: none;
}

/* Десктопная версия */
.desktop-filter-v2 {
    display: block;
}

.city-buttons-v2 {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.city-btn-v2 {
    padding: 12px 24px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.city-btn-v2:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #222222;
    transform: translateY(-1px);
}

.city-btn-v2.active {
    background: #27ae60;
    border-color: #27ae60;
    color: white;
}

.city-btn-v2.more-cities-btn-v2 {
    background: #222222;
    border-color: #222222;
    color: white;
    font-weight: 600;
}

.city-btn-v2.more-cities-btn-v2:hover {
    background: #333333;
    border-color: #333333;
    transform: translateY(-2px);
}

.arrow-icon-v2 {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

/* Мобильная кнопка поиска */
.mobile-search-btn-v2 {
    width: 100%;
    padding: 14px 20px;
    background: #27ae60;
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
}

.mobile-search-btn-v2 svg {
    width: 22px;
    height: 22px;
    fill: white;
}

.mobile-search-btn-v2:active {
    transform: scale(0.98);
}

.selected-city-badge-v2 {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #222222;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Модальное окно */
.city-modal-v2-improved {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.city-modal-v2-improved.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.modal-content-v2-improved {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 800px;
    max-height: 80vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header-v2-improved {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 2px solid #f1f5f9;
    background: #fefefe;
}

.modal-header-v2-improved h3 {
    font-size: 22px;
    font-weight: 700;
    color: #222222;
    margin: 0;
}

.close-btn-v2-improved {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-btn-v2-improved svg {
    width: 22px;
    height: 22px;
    fill: #64748b;
}

.close-btn-v2-improved:hover {
    background: #e2e8f0;
}

.close-btn-v2-improved:hover svg {
    fill: #222222;
}

.modal-body-v2-improved {
    padding: 28px;
    overflow-y: auto;
}

.modal-grid-v2-improved {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}

.modal-city-btn-v2-improved {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    color: #475569;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.modal-city-btn-v2-improved svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
    flex-shrink: 0;
}

.modal-city-btn-v2-improved:hover {
    background: #27ae60;
    border-color: #27ae60;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39,174,96,0.3);
}

.modal-city-btn-v2-improved.active {
    background: #27ae60;
    border-color: #27ae60;
    color: white;
}

/* Адаптивный дизайн */
@media (max-width: 768px) {
    .filter-container-v2-improved {
        padding: 0px;
        background: none;
        border: none;
        box-shadow: none;
    }
    
    .city-btn-v2 {
        padding: 10px 18px;
        font-size: 13px;
    }
    
    .modal-content-v2-improved {
        max-width: 95%;
        max-height: 85vh;
    }
    
    .modal-header-v2-improved {
        padding: 20px;
    }
    
    .modal-header-v2-improved h3 {
        font-size: 18px;
    }
    
    .modal-body-v2-improved {
        padding: 20px;
    }
    
    .modal-grid-v2-improved {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
    }
    
    .modal-city-btn-v2-improved {
        padding: 12px 14px;
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    /* На мобильных прячем десктопную версию */
    .desktop-filter-v2 {
        display: none;
    }
    
    /* Показываем мобильную версию */
    .mobile-filter-v2 {
        display: block;
    }
    
    .filter-container-v2-improved {
        padding: 0px;
        border-radius: 12px;
    }
    
    .modal-grid-v2-improved {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .modal-city-btn-v2-improved {
        font-size: 12px;
        padding: 10px 12px;
    }
}
</style>

<script>
// Открыть модальное окно
function openModalV2Improved() {
    document.getElementById('cityModalV2Improved').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Закрыть модальное окно
function closeModalV2Improved(event) {
    if (!event || event.target.id === 'cityModalV2Improved') {
        document.getElementById('cityModalV2Improved').classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Выбрать город - РЕАЛЬНАЯ ФИЛЬТРАЦИЯ
function selectCityV2Improved(cityName) {
    // Формируем URL с параметром города
    const url = new URL(window.location.href);
    
    if (cityName === 'all') {
        // Если выбран "Все города", убираем параметр city
        url.searchParams.delete('city');
    } else {
        // Устанавливаем параметр city
        url.searchParams.set('city', cityName);
    }
    
    // Переходим на новый URL
    window.location.href = url.toString();
}

// Клик по кнопке города в десктопной версии
document.querySelectorAll('.city-btn-v2:not(.more-cities-btn-v2)').forEach(btn => {
    btn.addEventListener('click', function() {
        const city = this.getAttribute('data-city');
        selectCityV2Improved(city);
    });
});

// Закрытие модального окна по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModalV2Improved();
    }
});
</script>