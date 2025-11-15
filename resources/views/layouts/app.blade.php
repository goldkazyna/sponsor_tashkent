<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Спонсоры и Содержанки Ташкент')</title>
	<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body>
<style>
    /* Кнопка "Проверенный спонсор" */
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

    /* Sticky header */
    header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: white;
        transition: all 0.3s ease;
    }

    header.scrolled {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    header.scrolled .top-menu {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }

    .top-menu {
        max-height: 100px;
        opacity: 1;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }

    header.scrolled .main-menu {
        padding: 0.75rem 0;
    }

    header.scrolled .logo img {
        height: 40px !important;
    }

    header.scrolled .btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    header.scrolled .btn-verified-text {
        font-size: 0.7rem;
    }

    /* Плавные переходы */
    .main-menu {
        transition: padding 0.3s ease;
    }

    .logo img {
        transition: height 0.3s ease;
    }

    .btn {
        transition: all 0.3s ease;
    }

    /* Мобильная кнопка "Купить статус" */
    .mobile-verified-btn-wrapper {
        display: none;
    }

    @media (max-width: 768px) {
        .main-menu {
            background: #f8f9fa;
            padding: 5px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .mobile-verified-btn-wrapper {
            display: block;
            padding: 0;
            margin: 0 0 1.5rem 0;
        }

        .mobile-verified-btn {
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

        .mobile-verified-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            text-decoration: none;
            color: white;
        }

        .mobile-verified-btn svg:first-child {
            width: 24px;
            height: 24px;
            fill: white;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .mobile-verified-btn span {
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
					<a href="/"><img src="{{ asset('images/logo.png') }}" alt="Спонсоры Ташкент" style="height: 50px;"></a>
				</div>
				<!-- Кнопки для десктопа -->
				<div class="buttons">
					<a href="/pricing" class="btn btn-verified">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: white; margin-right: 5px;">
							<path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
						</svg>
						<span class="btn-verified-text">Купить статус проверенного спонсора</span>
					</a>
					<a href="/add" class="btn btn-success">+ Добавить объявление</a>
					
					@if(session('user_id'))
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
        <!-- Мобильная кнопка "Купить статус" -->
        <div class="mobile-verified-btn-wrapper">
            <a href="/pricing" class="mobile-verified-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                </svg>
                <span>Купить статус проверенного спонсора</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: white;">
                    <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                </svg>
            </a>
        </div>

        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 Спонсоры и Содержанки Ташкент. Все права защищены.</p>
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