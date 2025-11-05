<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Спонсоры и Содержанки Ташкент')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        header { background: #2c3e50; color: white; padding: 20px; }
        header h1 { font-size: 24px; }
        nav { margin-top: 10px; }
        nav a { color: white; margin-right: 20px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        footer { background: #34495e; color: white; padding: 20px; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>💎 Спонсоры и Содержанки Ташкент</h1>
            <nav>
                <a href="/">Главная</a>
                <a href="/sponsors">Спонсоры</a>
                <a href="/girls">Содержанки</a>
                <a href="/login">Вход</a>
                <a href="/register">Регистрация</a>
            </nav>
        </div>
    </header>

    <div class="container">
        @yield('content')
    </div>

    <footer>
        <p>&copy; 2025 Спонсоры и Содержанки Ташкент. Все права защищены.</p>
        <p>Только для лиц старше 18 лет</p>
    </footer>
</body>
</html>