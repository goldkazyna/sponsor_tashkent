<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Знакомства в Казахстане | знакомства.KZ')</title>
    <meta name="description" content="@yield('meta_description', 'Сайт знакомств в Казахстане. Анкеты девушек и мужчин в Алматы, Астане и других городах.')">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta name="yandex-verification" content="146f8ad2330863be" />
    <meta name="verification" content="2b16d45900f2e4cde6c0335a7ce05d" />
	<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
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
					<a href="/"><img src="{{ asset('images/logo.png') }}" alt="знакомства.KZ" style="height: 50px;"></a>
				</div>
				<!-- Кнопки для десктопа -->
				<?php
				// Проверяем авторизацию и статус пользователя
				$currentUser = null;
				$isVerified = false;

				$unreadMessages = 0;

				if (session('user_id')) {
					$currentUser = DB::table('users')->where('id', session('user_id'))->first();
					if ($currentUser) {
						$isVerified = $currentUser->prov == 1;
						$unreadMessages = DB::table('messages')
							->where('receiver_id', $currentUser->id)
							->where('is_read', 0)
							->count();
					}
				}
				?>

				<!-- ДЕСКТОПНАЯ ВЕРСИЯ - в главном меню -->
				<div class="buttons">
					{{-- Кнопка «Купить статус» временно убрана --}}
					
										
					@if($currentUser)
						<!-- Авторизованный пользователь -->
						<a href="{{ route('profile.index') }}" class="btn btn-outline" style="position: relative;">Мой профиль@if($unreadMessages > 0)<span style="position:absolute; top:-7px; right:-7px; background:#ef4444; color:#fff; min-width:19px; height:19px; padding:0 5px; border-radius:10px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.25); line-height:1;">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>@endif</a>
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
						
			@if(session('user_id'))
				<!-- Авторизованный пользователь -->
				<a href="{{ route('profile.index') }}" class="btn btn-outline" style="position: relative;">Мой профиль@if($unreadMessages > 0)<span style="position:absolute; top:-7px; right:-7px; background:#ef4444; color:#fff; min-width:19px; height:19px; padding:0 5px; border-radius:10px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.25); line-height:1;">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>@endif</a>
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
        {{-- Мобильная кнопка «Купить статус» временно убрана --}}

        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 знакомства.KZ. Все права защищены.</p>
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