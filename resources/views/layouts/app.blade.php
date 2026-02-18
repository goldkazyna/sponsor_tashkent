<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Спонсоры и Содержанки Казахстан')</title>
    <meta name="description" content="@yield('meta_description', 'Спонсоры и Содержанки в Казахстане. Найдите спонсора или содержанку в Алматы, Астане и других городах.')">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta name="yandex-verification" content="146f8ad2330863be" />
    <meta name="verification" content="2b16d45900f2e4cde6c0335a7ce05d" />
	<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function(m,e,t,r,i,k,a){
			m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
			m[i].l=1*new Date();
			for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
			k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
		})(window, document,'script','https://mc.yandex.ru/metrika/tag.js', 'ym');
		ym(85939578, 'init', {clickmap:true, referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/85939578" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
</head>
<body>
<style>
    .btn-verified {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    border: none !important;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    padding: 0.6rem 1.2rem !important;
}

.btn-verified-text {
    font-size: 0.8rem;
    line-height: 1.3;
    text-align: left;
}

.btn-verified::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-verified:hover::before {
    left: 100%;
}

.btn-verified:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

/* Десктоп - статус "Проверен" */
.btn-verified-active {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%) !important;
    color: white !important;
    border: none !important;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    padding: 0.6rem 1.2rem !important;
    cursor: default;
    position: relative;
    overflow: hidden;
}

.btn-verified-active::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: translate(-50%, -50%);
    animation: ripple 2s infinite;
}

@keyframes ripple {
    0% {
        width: 0;
        height: 0;
        opacity: 0.5;
    }
    100% {
        width: 300px;
        height: 300px;
        opacity: 0;
    }
}

/* Мобильная версия */
.mobile-verified-btn-wrapper {
    display: none;
}

@media (max-width: 768px) {
    .mobile-verified-btn-wrapper {
        display: block;
        padding: 0;
        margin: 0 0 1.5rem 0;
    }

    /* Мобильная кнопка "Купить" */
    .mobile-buy-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
    }

    .mobile-buy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        text-decoration: none;
        color: white;
    }

    .mobile-buy-btn svg:first-child {
        width: 24px;
        height: 24px;
        fill: white;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .mobile-buy-btn span {
        flex: 1;
    }

    /* Мобильный бейдж "Проверен" */
    .mobile-verified-active {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        cursor: default;
    }

    .mobile-verified-active svg:first-child {
        width: 24px;
        height: 24px;
        fill: white;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .mobile-verified-active span {
        flex: 1;
    }
}
    </style>
    <header id="mainHeader">
        <!-- Верхнее меню (только десктоп) -->
        <div class="top-menu">
            <div class="container">
                <nav>
                    <a href="/">Главная</a>
                    <a href="/contact">Написать админу</a>
                    <a href="/rules">Правила</a>
                    <a href="/news">Нововведения</a>
                </nav>
            </div>
        </div>
        
        <!-- Главное меню: логотип и кнопки -->
        <div class="main-menu">
            <div class="container">
                <div class="logo">
					<a href="/"><img src="{{ asset('images/logo.png') }}" alt="Спонсоры Казахстан" style="height: 50px;"></a>
				</div>
				<!-- Кнопки для десктопа -->
				<?php
				// Проверяем авторизацию и статус пользователя
				$currentUser = null;
				$isVerified = false;

				if (session('user_id')) {
					$currentUser = DB::table('users')->where('id', session('user_id'))->first();
					if ($currentUser) {
						$isVerified = $currentUser->prov == 1;
					}
				}
				?>

				<!-- ДЕСКТОПНАЯ ВЕРСИЯ - в главном меню -->
				<div class="buttons">
					@if(!$currentUser)
						<!-- Гость - показываем кнопку "Купить статус" -->
						<a href="/become-verified" class="btn btn-verified">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: white; margin-right: 5px;">
								<path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
							</svg>
							<span class="btn-verified-text">Купить статус проверенного пользователя</span>
						</a>
					@elseif(!$isVerified)
						<!-- Авторизован, но НЕ проверен - показываем кнопку -->
						<a href="/become-verified" class="btn btn-verified">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: white; margin-right: 5px;">
								<path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
							</svg>
							<span class="btn-verified-text">Купить статус проверенного пользователя</span>
						</a>
					@else
						<!-- Авторизован И проверен - показываем бейдж -->
						<div class="btn btn-verified-active">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: white; margin-right: 6px;">
								<path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
							</svg>
							<span class="btn-verified-text">Проверенный пользователь</span>
						</div>
					@endif
					
					<a href="/add" class="btn btn-success">+ Добавить объявление</a>
					
					@if($currentUser)
						<!-- Авторизованный пользователь -->
						<a href="{{ route('profile.index') }}" class="btn btn-outline">Мой профиль</a>
						<form method="POST" action="{{ route('logout') }}" style="display: inline;">
							@csrf
							<button type="submit" class="btn btn-outline" style="cursor: pointer;">Выйти</button>
						</form>
					@else
						<!-- Гость -->
						<a href="/login" class="btn btn-outline">Войти</a>
						<a href="/register" class="btn btn-outline">Регистрация</a>
					@endif
				</div>

	                <!-- Бургер для мобилки -->
                <div class="burger" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Мобильное меню -->
	<div class="overlay" id="overlay" onclick="toggleMenu()"></div>
	<div class="mobile-menu" id="mobileMenu">
		<div class="mobile-menu-header">
			<span class="close-menu" onclick="toggleMenu()">&times;</span>
		</div>
		<nav>
			<a href="/">Главная</a>
			<a href="/contact">Написать админу</a>
			<a href="/rules">Правила</a>
			<a href="/news">Нововведения</a>
		</nav>
		<div class="buttons">
			<a href="/add" class="btn btn-success">+ Добавить объявление</a>
			
			@if(session('user_id'))
				<!-- Авторизованный пользователь -->
				<a href="{{ route('profile.index') }}" class="btn btn-outline">Мой профиль</a>
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button type="submit" class="btn btn-outline" style="width: 100%; cursor: pointer;">Выйти</button>
				</form>
			@else
				<!-- Гость -->
				<a href="/login" class="btn btn-outline">Войти</a>
				<a href="/register" class="btn btn-outline">Регистрация</a>
			@endif
		</div>
	</div>

    <div class="container">
        <!-- МОБИЛЬНАЯ ВЕРСИЯ - кнопка "Купить статус" -->
        <div class="mobile-verified-btn-wrapper">
            @if(!$currentUser)
                <a href="/become-verified" class="mobile-verified-btn mobile-buy-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                    </svg>
                    <span>Купить статус проверенного пользователя</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: white;">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
            @elseif(!$isVerified)
                <a href="/become-verified" class="mobile-verified-btn mobile-buy-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                    </svg>
                    <span>Купить статус проверенного пользователя</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: white;">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
            @else
                <div class="mobile-verified-btn mobile-verified-active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                    </svg>
                    <span>Вы - проверенный пользователь</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: white;">
                        <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Telegram плашка (только на главной) -->
        @if(Route::currentRouteName() === 'home')
        <div style="max-width: 1160px; margin: 15px auto; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
            <div style="background: linear-gradient(135deg, #f5a623, #e8941a); text-align: center; padding: 10px 15px; font-weight: 600; color: #fff; font-size: 14px;">
                🔒 Сайт блокируется - используйте VPN
            </div>
            <div style="background: linear-gradient(135deg, #d64541, #c0392b); text-align: center; padding: 15px;">
                <div style="color: #fff; font-weight: 700; font-size: 15px; margin-bottom: 12px;">📱 Telegram группы:</div>
                <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                    <a href="https://t.me/sponsory5" target="_blank" style="background: rgba(0,0,0,0.25); color: #fff; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: #fff;"><path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/></svg>
                        Информационная группа
                    </a>
                    <a href="https://t.me/blacklist_sponsory" target="_blank" style="background: rgba(0,0,0,0.25); color: #fff; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: #fff;"><path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/></svg>
                        Канал черный список
                    </a>
                    <a href="https://t.me/sponsory5_bot" target="_blank" style="background: rgba(0,0,0,0.25); color: #fff; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: #fff;"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M17.13,17C15.92,18.85 14.11,20.24 12,20.92C9.89,20.24 8.08,18.85 6.87,17C6.53,16.5 6.24,16 6,15.47C6,13.82 8.71,12.47 12,12.47C15.29,12.47 18,13.79 18,15.47C17.76,16 17.47,16.5 17.13,17Z"/></svg>
                        Подать жалобу
                    </a>
                </div>
            </div>
        </div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 Спонсоры и Содержанки Казахстан. Все права защищены.</p>
        <p>Только для лиц старше 18 лет</p>
    </footer>

    

    <script>
        function toggleMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('overlay');
            mobileMenu.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Sticky header при скролле (с защитой от дёрганья)
        let lastScroll = 0;
        let scrollTimeout = null;
        const header = document.getElementById('mainHeader');

        window.addEventListener('scroll', () => {
            // Очищаем предыдущий таймер
            clearTimeout(scrollTimeout);

            // Добавляем небольшую задержку чтобы избежать дёрганья
            scrollTimeout = setTimeout(() => {
                const currentScroll = window.pageYOffset;

                if (currentScroll > 50) { // Увеличили порог до 100px
                    header.classList.add('scrolled');
                } else if (currentScroll < 10) { // Убираем класс только если прокрутили выше 50px
                    header.classList.remove('scrolled');
                }

                lastScroll = currentScroll;
            }, 10); // Задержка 50ms
        });
    </script>
</body>
</html>