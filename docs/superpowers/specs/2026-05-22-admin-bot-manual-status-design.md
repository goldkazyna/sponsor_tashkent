# Админ-бот: ручная выдача статуса и ТОП

**Дата:** 2026-05-22

## Цель

Отдельный Telegram-бот **только для админа**, который вручную выдаёт
«Проверенный спонсор» (`prov=1`) и поднимает объявление в ТОП — без
онлайн-оплаты (она отключена). Делает ровно то же, что делал бы
`PaymentController::callback()` после успешной оплаты.

## Доступ

- Токен бота: env `TELEGRAM_BOT_TOKEN_ADMIN`.
- Whitelist: env `TELEGRAM_ADMIN_IDS` (Telegram ID через запятую).
- Не-админу бот отвечает `⛔ Доступ запрещён. Ваш Telegram ID: <id>` —
  так можно узнать свой ID для первичной настройки env.

## Инфраструктура

- Контроллер `app/Http/Controllers/AdminBotController.php` — по образцу
  `TelegramBotController`: свой `pollUpdates()`, offset-кеш
  `admin_bot_poll_offset`, lock `admin_bot_poll`, тот же SOCKS5-прокси,
  лог-канал `telegram`.
- Роуты: `GET /admin-bot/poll-secret` (cron), `GET /admin-bot/disable-webhook-secret`.
- Состояние диалога: Cache-ключ `admin_bot_state_{telegramId}` (TTL 30 мин).
- Webhook не используется (Reg.ru блокирует IP Telegram) — long polling по cron.

## Флоу

1. `/start` → «Введите email пользователя».
2. Email → поиск в `users` (LOWER(email), точное совпадение).
   - не найден → ошибка, просим снова;
   - найден → 2 inline-кнопки: «✅ Проверенный спонсор» / «🚀 Оплата ТОП».
3. Спонсор → кнопка «30 дней» → сводка.
   ТОП → список активных объявлений (`post.email = email AND del = 0`,
   до 20 шт.) → выбор → «30 дней» → сводка.
4. Сводка (email, ФИО, услуга, срок, дата «до») → «✅ Подтвердить» / «❌ Отмена».
5. Подтвердить → активация → сообщение об успехе. Сброс состояния.

## Логика активации (1-в-1 с PaymentController::callback)

- **Спонсор:** `prov=1`, `prov_date = (статус ещё активен ? текущая prov_date : сейчас) + days`.
- **ТОП:** сброс `count_view=0` у всех; запись в `top_post` либо продление
  `date_end` (если уже в ТОПе и срок не вышел), либо вставка
  (`id_post`, `date`, `date_end`, `count_view=0`).

Логика скопирована в бот (PaymentController не трогаем — изоляция).

## Что нужно на проде

1. В `.env`: `TELEGRAM_BOT_TOKEN_ADMIN=<токен>` и `TELEGRAM_ADMIN_IDS=<твой id>`.
   - Если ID не знаешь: оставь `TELEGRAM_ADMIN_IDS` пустым, напиши боту — он
     ответит твой ID, впиши его в env.
2. После деплоя — `/clear-cache-secret`.
3. Один раз открыть `/admin-bot/disable-webhook-secret`.
4. Завести cron в Plesk на `https://<домен>/admin-bot/poll-secret`, каждую минуту.

## Вне рамок

- Никаких новых таблиц/миграций.
- Только срок 30 дней.
- PaymentController и онлайн-оплата не меняются.
