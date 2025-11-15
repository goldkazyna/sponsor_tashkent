<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Спонсоры и Содержанки Ташкент')</title>
	<link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>
<body>
    <header>
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
					<a href="/pricing" class="btn btn-primary">Платные услуги сайта</a>
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
			<a href="/services" class="btn btn-primary">Платные услуги сайта</a>
			<a href="/add" class="btn btn-success">+ Добавить объявление</a>
			
			@if(session('user_id'))
				<!-- Авторизованный пользователь -->
				<a href="/profile" class="btn btn-outline">Мой профиль</a>
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
    </script>
</body>
</html>