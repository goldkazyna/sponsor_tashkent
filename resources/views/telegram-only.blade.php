<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Сервис работает в Telegram</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 45%, #ede9fe 100%);
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(190, 24, 93, 0.15);
            padding: 40px 32px;
            text-align: center;
        }
        .tg-icon {
            width: 84px;
            height: 84px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2aabee, #229ed9);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tg-icon svg { width: 46px; height: 46px; fill: #fff; }
        h1 { font-size: 24px; line-height: 1.3; margin-bottom: 14px; color: #be185d; }
        p { font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 12px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
            padding: 16px 30px;
            background: linear-gradient(135deg, #2aabee, #229ed9);
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 14px;
            transition: transform .15s ease, box-shadow .15s ease;
            box-shadow: 0 8px 20px rgba(34, 158, 217, 0.35);
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(34, 158, 217, 0.45); }
        .btn svg { width: 22px; height: 22px; fill: #fff; }
        .admin {
            margin-top: 26px;
            font-size: 14px;
            color: #6b7280;
        }
        .admin a { color: #be185d; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="tg-icon">
            <svg viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71l-4.14-3.05-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
        </div>
        <h1>Сервис теперь работает<br>только в Telegram</h1>
        <p>Все объявления, поиск и общение переехали в наш Telegram-бот. Это быстрее и удобнее — открывайте бота и продолжайте прямо там.</p>
        <a class="btn" href="https://t.me/newsponsorykz_bot" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71l-4.14-3.05-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
            Открыть бота
        </a>
        <div class="admin">
            По вопросам — <a href="https://t.me/Sponsor_admin" target="_blank" rel="noopener">@Sponsor_admin</a>
        </div>
    </div>
</body>
</html>
