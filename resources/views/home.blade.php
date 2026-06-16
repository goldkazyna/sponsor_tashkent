{{--
    Главная в режиме закрытия сервиса: ТОЛЬКО сообщение о возврате средств.
    Намеренно НЕ наследует layouts.app (без шапки, меню, баннера, футера).
    Вернуть объявления — восстановить прежнюю версию из git (см. коммиты до 2026-06-16).
--}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Возврат средств</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .box {
            max-width: 560px;
            width: 100%;
            background: #fff;
            border: 2px solid #f59e0b;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.10);
            padding: 36px 30px;
            text-align: center;
        }
        .box .icon { font-size: 2.4rem; margin-bottom: 14px; }
        .box h1 {
            color: #b91c1c;
            font-size: 2.4rem;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
            line-height: 1.15;
        }
        @media (max-width: 480px) { .box h1 { font-size: 1.8rem; } }
        .box p {
            color: #334155;
            font-size: 1.15rem;
            line-height: 1.7;
        }
        .box p strong { color: #b45309; }
        .box a {
            color: #0077b3;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Сервис закрыт</h1>
        <div class="icon">💳</div>
        <p>
            <strong>Возврат средств:</strong> если у вас оплачен статус и осталось больше 15 дней —
            <a href="https://t.me/Sponsor_admin" target="_blank" rel="noopener">напишите нам</a>, вернём деньги.
        </p>
    </div>
</body>
</html>
