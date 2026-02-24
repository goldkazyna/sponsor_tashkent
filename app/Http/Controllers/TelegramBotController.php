<?php

namespace App\Http\Controllers;

use App\Services\AiModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    private string $botToken;

    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token_interactive');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Одноразовая установка webhook URL.
     */
    public function setWebhook(Request $request)
    {
        $url = url('/telegram/webhook');

        $response = Http::post("{$this->apiUrl}/setWebhook", [
            'url' => $url,
        ]);

        return response()->json($response->json());
    }

    /**
     * Приём webhook от Telegram.
     */
    public function webhook(Request $request)
    {
        $update = $request->all();

        Log::info('Telegram webhook received', $update);

        // Обработка inline кнопок (callback_query)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);

            return response()->json(['ok' => true]);
        }

        if (! isset($update['message'])) {
            return response()->json(['ok' => true]);
        }

        $message = $update['message'];
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'];
        $telegramId = $message['from']['id'];
        $username = $message['from']['username'] ?? null;

        if ($text === '/start') {
            Cache::forget("tg_state_{$telegramId}");
            $this->handleStart($chatId, $telegramId, $username);

            return response()->json(['ok' => true]);
        }

        // Проверяем состояние диалога (авторизация/регистрация)
        $state = Cache::get("tg_state_{$telegramId}");

        if ($state) {
            $this->handleDialogFlow($chatId, $telegramId, $username, $text, $state);
        } else {
            $this->handleTextButton($chatId, $telegramId, $text);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Обработка команды /start.
     */
    private function handleStart(int $chatId, int $telegramId, ?string $username): void
    {
        $this->sendMessage($chatId, 'Добро пожаловать! Выберите действие:', $this->getGuestKeyboard());
    }

    /**
     * Процесс авторизации/регистрации по шагам.
     */
    private function handleDialogFlow(int $chatId, int $telegramId, ?string $username, string $text, array $state): void
    {
        // Отмена на любом шаге
        if ($text === '❌ Отмена') {
            Cache::forget("tg_state_{$telegramId}");
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
            $this->sendMessage($chatId, 'Действие отменено.', $keyboard);

            return;
        }

        // === ДОБАВЛЕНИЕ ОБЪЯВЛЕНИЯ ===
        if (str_starts_with($state['step'], 'post_')) {
            $this->handlePostDialogFlow($chatId, $telegramId, $username, $text, $state);

            return;
        }

        // === КУПИТЬ СТАТУС: помощь ===
        if ($state['step'] === 'buy_status_telegram') {
            $this->sendBuyStatusHelp($chatId, $telegramId, $text);

            return;
        }

        // === НАПИСАТЬ АДМИНУ ===
        if ($state['step'] === 'admin_message') {
            $this->sendAdminMessage($chatId, $telegramId, $text);

            return;
        }

        switch ($state['step']) {
            // === АВТОРИЗАЦИЯ ===
            case 'login_email':
                $email = trim(mb_strtolower($text));
                $user = DB::table('users')->where('email', $email)->first();

                if (! $user) {
                    $this->sendMessage($chatId, "❌ Пользователь с таким email не найден.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");

                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'login_password',
                    'user_id' => $user->id,
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, '🔑 Введите пароль:');
                break;

            case 'login_password':
                $user = DB::table('users')->where('id', $state['user_id'])->first();

                if (! $user || $user->password !== sha1(md5($text))) {
                    $this->sendMessage($chatId, "❌ Неверный пароль.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");

                    return;
                }

                DB::table('users')->where('id', $user->id)->update([
                    'telegram_id' => $telegramId,
                    'telegram_username' => $username,
                ]);

                Cache::forget("tg_state_{$telegramId}");
                $this->sendMessage($chatId, '✅ Вы успешно авторизованы!', $this->getAuthKeyboard());
                break;

                // === РЕГИСТРАЦИЯ ===
            case 'register_email':
                $email = trim(mb_strtolower($text));

                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->sendMessage($chatId, "❌ Некорректный email.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");

                    return;
                }

                $exists = DB::table('users')->where('email', $email)->exists();
                if ($exists) {
                    $this->sendMessage($chatId, "❌ Этот email уже зарегистрирован.\n\nПопробуйте другой или нажмите «❌ Отмена»:");

                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'register_sex',
                    'email' => $email,
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, '👤 Выберите ваш пол:', [
                    'inline_keyboard' => [
                        [
                            ['text' => '👨 Мужчина', 'callback_data' => 'sex_1'],
                            ['text' => '👩 Женщина', 'callback_data' => 'sex_2'],
                        ],
                    ],
                ]);
                break;
        }
    }

    /**
     * Обработка inline кнопок.
     */
    private function handleCallbackQuery(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'];
        $telegramId = $callback['from']['id'];
        $username = $callback['from']['username'] ?? null;
        $data = $callback['data'];

        // Подтверждаем нажатие
        Http::post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callback['id'],
        ]);

        // Пагинация объявлений: posts_page_{page}_{cityId}
        if (str_starts_with($data, 'posts_page_')) {
            $parts = explode('_', str_replace('posts_page_', '', $data));
            $page = (int) ($parts[0] ?? 1);
            $cityId = (int) ($parts[1] ?? 0);
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $this->handleViewPosts($chatId, $user, $page, $cityId);

            return;
        }

        // Пагинация моих объявлений: myposts_page_{page}
        if (str_starts_with($data, 'myposts_page_')) {
            $page = (int) str_replace('myposts_page_', '', $data);
            $this->handleMyPosts($chatId, $telegramId, $page);

            return;
        }

        // Возврат в главное меню
        if ($data === 'back_to_menu') {
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
            $this->sendMessage($chatId, '📋 Главное меню:', $keyboard);

            return;
        }

        // === ДОБАВЛЕНИЕ ОБЪЯВЛЕНИЯ: callback-кнопки ===

        // Выбор города
        if (str_starts_with($data, 'post_city_')) {
            $cityId = (int) str_replace('post_city_', '', $data);
            $state = Cache::get("tg_state_{$telegramId}");
            if (! $state || $state['step'] !== 'post_city') {
                $this->sendMessage($chatId, '❌ Сессия истекла. Попробуйте заново.', $this->getAuthKeyboard());

                return;
            }

            $state['post_data']['city'] = $cityId;
            $state['step'] = 'post_whats';
            Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

            $this->sendMessage($chatId, "📱 Введите номер WhatsApp:\n<i>Формат: +77001234567</i>", [
                'inline_keyboard' => [
                    [['text' => '⏩ Пропустить', 'callback_data' => 'post_skip_whats']],
                ],
            ]);

            return;
        }

        // Пропуск телефона
        if ($data === 'post_skip_phone') {
            $state = Cache::get("tg_state_{$telegramId}");
            if (! $state || $state['step'] !== 'post_phone') {
                return;
            }

            $state['post_data']['phone'] = '';
            $state['step'] = 'post_city';
            Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

            $this->sendCityInlineKeyboard($chatId);

            return;
        }

        // Пропуск WhatsApp
        if ($data === 'post_skip_whats') {
            $state = Cache::get("tg_state_{$telegramId}");
            if (! $state || $state['step'] !== 'post_whats') {
                return;
            }

            $state['post_data']['whats'] = '';
            $state['step'] = 'post_telegram';
            Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

            $this->sendMessage($chatId, "💬 Введите ваш Telegram:\n<i>Формат: @username</i>", [
                'inline_keyboard' => [
                    [['text' => '⏩ Пропустить', 'callback_data' => 'post_skip_telegram']],
                ],
            ]);

            return;
        }

        // Пропуск Telegram
        if ($data === 'post_skip_telegram') {
            $state = Cache::get("tg_state_{$telegramId}");
            if (! $state || $state['step'] !== 'post_telegram') {
                return;
            }

            $state['post_data']['telegram'] = '';
            $state['step'] = 'post_description';
            Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

            $this->sendMessage($chatId, "💬 Введите описание объявления:\n<i>Расскажите о себе и о том, кого ищете...</i>");

            return;
        }

        // Опубликовать
        if ($data === 'post_publish') {
            $this->publishPost($chatId, $telegramId, $username);

            return;
        }

        // Отменить
        if ($data === 'post_cancel') {
            Cache::forget("tg_state_{$telegramId}");
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
            $this->sendMessage($chatId, '❌ Добавление объявления отменено.', $keyboard);

            return;
        }

        // === МОИ ОБЪЯВЛЕНИЯ: callback-кнопки ===

        // Поднять в ТОП
        if (str_starts_with($data, 'mypost_top_')) {
            $postId = (int) str_replace('mypost_top_', '', $data);
            $this->handleBoostTop($chatId, $telegramId, $postId);

            return;
        }

        // Удалить объявление
        if (str_starts_with($data, 'mypost_del_')) {
            $postId = (int) str_replace('mypost_del_', '', $data);
            $state = Cache::get("tg_state_{$telegramId}");

            // Если ещё не подтвердил — спрашиваем
            if (! $state || ($state['step'] ?? '') !== 'confirm_delete' || ($state['post_id'] ?? 0) !== $postId) {
                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'confirm_delete',
                    'post_id' => $postId,
                ], now()->addMinutes(5));

                $this->sendMessage($chatId, '⚠️ Вы уверены, что хотите удалить это объявление?', [
                    'inline_keyboard' => [
                        [
                            ['text' => '✅ Да, удалить', 'callback_data' => "mypost_del_{$postId}"],
                            ['text' => '❌ Нет', 'callback_data' => 'mypost_del_cancel'],
                        ],
                    ],
                ]);

                return;
            }

            // Подтверждено — удаляем (soft delete)
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $post = DB::table('post')->where('id', $postId)->where('del', 0)->first();

            if (! $post || ! $user || ($post->email !== $user->email && (string) $post->telegram_id !== (string) $telegramId)) {
                Cache::forget("tg_state_{$telegramId}");
                $this->sendMessage($chatId, '❌ Объявление не найдено или у вас нет прав на удаление.');

                return;
            }

            DB::table('post')->where('id', $postId)->update(['del' => 1]);
            Cache::forget("tg_state_{$telegramId}");

            $this->sendMessage($chatId, '✅ Объявление удалено.', $this->getAuthKeyboard());

            return;
        }

        // Отмена удаления
        if ($data === 'mypost_del_cancel') {
            Cache::forget("tg_state_{$telegramId}");
            $this->sendMessage($chatId, '👌 Удаление отменено.', $this->getAuthKeyboard());

            return;
        }

        // Помощь с покупкой статуса — запрашиваем Telegram для связи
        if ($data === 'buy_status_help') {
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();

            Cache::put("tg_state_{$telegramId}", [
                'step' => 'buy_status_telegram',
            ], now()->addMinutes(10));

            $this->sendMessage($chatId, "💬 Введите ваш Telegram для связи:\n<i>Формат: @username</i>", $this->getCancelKeyboard());

            return;
        }

        if (in_array($data, ['sex_1', 'sex_2'])) {
            $state = Cache::get("tg_state_{$telegramId}");

            if (! $state || $state['step'] !== 'register_sex') {
                $this->sendMessage($chatId, '❌ Сессия истекла. Начните регистрацию заново.', $this->getGuestKeyboard());

                return;
            }

            $sex = $data === 'sex_1' ? 1 : 2;
            $email = $state['email'];
            $password = bin2hex(random_bytes(4));

            DB::table('users')->insert([
                'email' => $email,
                'password' => sha1(md5($password)),
                'sex' => $sex,
                'ip' => '',
                'date' => now(),
                'activate' => 1,
                'confirm' => 1,
                'status' => 0,
                'phone' => '',
                'fio' => '',
                'activate_code' => '',
                'restore_code' => '',
                'prov' => 0,
                'telegram_id' => $telegramId,
                'telegram_username' => $username,
                'device_key' => uniqid('device_', true),
            ]);

            Cache::forget("tg_state_{$telegramId}");

            $sexLabel = $sex === 1 ? '👨 Мужчина' : '👩 Женщина';
            $this->sendMessage(
                $chatId,
                "✅ Регистрация завершена!\n\n"
                ."📧 Email: <b>{$email}</b>\n"
                ."🔑 Пароль: <code>{$password}</code>\n"
                ."👤 Пол: {$sexLabel}\n\n"
                .'Сохраните пароль — он нужен для входа на сайте.',
                $this->getAuthKeyboard()
            );
        }
    }

    /**
     * Обработка нажатий текстовых кнопок.
     */
    private function handleTextButton(int $chatId, int $telegramId, string $text): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        switch ($text) {
            case '🖊 Авторизоваться':
                if ($user) {
                    $this->sendMessage($chatId, 'Вы уже авторизованы!', $this->getAuthKeyboard());

                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'login_email',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, '📧 Введите ваш email:', $this->getCancelKeyboard());
                break;

            case '💾 Зарегистрироваться':
                if ($user) {
                    $this->sendMessage($chatId, 'Вы уже зарегистрированы!', $this->getAuthKeyboard());

                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'register_email',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, '📧 Введите ваш email:', $this->getCancelKeyboard());
                break;

            case '🔍 Просмотреть объявления':
                $this->sendMessage($chatId, '🏙 Выберите город или смотрите все объявления:', $this->getCityKeyboard());
                $this->handleViewPosts($chatId, $user, 1);
                break;

            case '➕ Добавить объявление':
                $this->handleAddPost($chatId, $telegramId);
                break;

            case '💻 Написать админу':
                if (! $user) {
                    $this->sendMessage($chatId, '❌ Для связи с админом необходимо авторизоваться.', $this->getGuestKeyboard());
                    break;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'admin_message',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, '💬 Напишите ваше сообщение админу:', $this->getCancelKeyboard());
                break;

            case '📄 Инструкция':
                $this->sendMessage($chatId,
                    "❗❗❗ Внимательно прочитайте инструкцию ❗❗❗\n\n"
                    ."✅ <b>Кто может давать объявления?</b>\n"
                    ."Объявления могут давать лица старше 18 лет:\n"
                    ."- Женщинам: бесплатно\n"
                    ."- Мужчинам: после покупки статуса проверенного спонсора\n"
                    ."🚫 Запрещается давать контакты третьих лиц\n\n"
                    ."✅ <b>Как купить статус проверенного спонсора?</b>\n"
                    ."1. Авторизуйтесь/зарегистрируйтесь в боте (кнопки в меню)\n"
                    ."2. Нажмите «📌 Купить статус»\n"
                    ."3. Оплатите по указанным реквизитам\n"
                    ."4. Напишите админу о платеже (@sponsor_admin)\n\n"
                    ."❗❗❗ <b>ВНИМАНИЕ!</b>\n"
                    ."Доступ открывается от 5 минут до 12 часов (в зависимости от загрузки)\n\n"
                    ."После активации при нажатии «📌 Купить статус» вы увидите подтверждение вашего статуса."
                );
                break;

            case '🚀 Топ объявлений':
            case '🚀 Топ объявления':
                $this->handleTopPosts($chatId, $user);
                break;

            case '📨 Мои объявления':
                $this->handleMyPosts($chatId, $telegramId);
                break;

            case '📌 Купить статус':
                $this->handleBuyStatus($chatId, $telegramId);
                break;

            case '❌ Выход':
                $this->sendMessage($chatId, '👋 Вы вышли из аккаунта.', $this->getGuestKeyboard());
                break;

            case '🏠 Главное меню':
                $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
                $this->sendMessage($chatId, '📋 Главное меню:', $keyboard);
                break;

            default:
                // Проверяем, не город ли это
                $city = DB::table('city')->where('title', $text)->first();
                if ($city) {
                    $this->handleViewPosts($chatId, $user, 1, $city->id);
                    break;
                }

                $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
                $this->sendMessage($chatId, 'Неизвестная команда. Выберите действие из меню:', $keyboard);
                break;
        }
    }

    /**
     * Просмотр объявлений с пагинацией.
     */
    private function handleViewPosts(int $chatId, ?object $user, int $page, int $cityId = 0): void
    {
        $perPage = 10;

        $query = DB::table('post')->where('del', 0);
        if ($cityId > 0) {
            $query->where('city', $cityId);
        }
        $total = $query->count();

        $totalPages = max(1, ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $postsQuery = DB::table('post')
            ->leftJoin('city', 'post.city', '=', 'city.id')
            ->select('post.*', 'city.title as city_name')
            ->where('post.del', 0);
        if ($cityId > 0) {
            $postsQuery->where('post.city', $cityId);
        }
        $posts = $postsQuery->orderByDesc('post.date')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        if ($posts->isEmpty()) {
            $this->sendMessage($chatId, '📭 Объявлений в этом городе пока нет.');

            return;
        }

        // ТОП объявление на первой странице
        if ($page === 1) {
            $this->showTopPost($chatId, $user);
        }

        foreach ($posts as $post) {
            $this->sendMessage($chatId, $this->formatPost($post, $user));
        }

        // Кнопки пагинации
        $cityParam = $cityId > 0 ? "_{$cityId}" : '_0';
        $buttons = [];
        if ($page > 1) {
            $buttons[] = ['text' => '◀ Предыдущая страница', 'callback_data' => 'posts_page_'.($page - 1).$cityParam];
        }
        if ($page < $totalPages) {
            $buttons[] = ['text' => '▶ Следующая страница', 'callback_data' => 'posts_page_'.($page + 1).$cityParam];
        }

        $inline = [];
        if (! empty($buttons)) {
            $inline[] = $buttons;
        }
        $inline[] = [['text' => '🔙 В главное меню', 'callback_data' => 'back_to_menu']];

        $this->sendMessage($chatId, "📄 Страница: {$page}/{$totalPages}", [
            'inline_keyboard' => $inline,
        ]);
    }

    /**
     * Показать ТОП объявление.
     */
    private function showTopPost(int $chatId, ?object $user): void
    {
        // Берём с наименьшим count_view, если одинаковы — рандом
        $topRecord = DB::table('top_post')
            ->where('date_end', '>=', now())
            ->orderBy('count_view', 'asc')
            ->inRandomOrder()
            ->first();

        if (! $topRecord) {
            return;
        }

        $post = DB::table('post')
            ->leftJoin('city', 'post.city', '=', 'city.id')
            ->select('post.*', 'city.title as city_name')
            ->where('post.id', $topRecord->id_post)
            ->where('post.del', 0)
            ->first();

        if (! $post) {
            return;
        }

        // Увеличиваем count_view на 1
        DB::table('top_post')->where('id', $topRecord->id)->increment('count_view');

        $text = "◆◆◆ТОП ОБЪЯВЛЕНИЕ◆◆◆\n".$this->formatPost($post, $user);

        $this->sendMessage($chatId, $text);
    }

    /**
     * Показать все ТОП объявления.
     */
    private function handleTopPosts(int $chatId, ?object $user): void
    {
        $topRecords = DB::table('top_post')
            ->where('date_end', '>=', now())
            ->orderBy('count_view', 'asc')
            ->get();

        if ($topRecords->isEmpty()) {
            $this->sendMessage($chatId, '📭 Топ объявлений пока нет.');

            return;
        }

        $count = 0;
        foreach ($topRecords as $topRecord) {
            $post = DB::table('post')
                ->leftJoin('city', 'post.city', '=', 'city.id')
                ->select('post.*', 'city.title as city_name')
                ->where('post.id', $topRecord->id_post)
                ->where('post.del', 0)
                ->first();

            if (! $post) {
                continue;
            }

            DB::table('top_post')->where('id', $topRecord->id)->increment('count_view');

            $text = "⭐ ТОП ОБЪЯВЛЕНИЕ ⭐\n" . $this->formatPost($post, $user);
            $this->sendMessage($chatId, $text);
            $count++;
        }

        if ($count === 0) {
            $this->sendMessage($chatId, '📭 Топ объявлений пока нет.');
        }
    }

    /**
     * Форматирование одного объявления.
     */
    private function formatPost(object $post, ?object $user): string
    {
        $hearts = $post->sex == 1 ? '💙💙💙' : '💗💗💗';
        $sexLabel = $post->sex == 1 ? '👦 Я: Мужчина' : '👩 Я: Женщина';
        $whoLabel = $post->who == 1 ? '👩 Ищу: Женщину' : '👦 Ищу: Мужчину';
        $date = date('d.m.Y H:i:s', strtotime($post->date));

        $text = "{$hearts}{$post->title}{$hearts}\n";
        $text .= "------------------------------\n";
        $cityName = $post->city_name ?? $post->city;
        $text .= "🚩 Казахстан/{$cityName}\n";
        $text .= "📅 {$date}\n";
        $text .= "{$sexLabel}\n";
        $text .= "{$whoLabel}\n";
        $text .= "------------------------------\n";

        if ($user) {
            $name = $post->fio ?: 'Не указано';
            $tg = $post->telegram ?: 'Не указан';
            if ($tg !== 'Не указан' && ! str_starts_with($tg, '@')) {
                $tg = '@'.$tg;
            }
            $text .= "👤 Имя: {$name}\n";
            $text .= "📩 Telegram: {$tg}\n";
        } else {
            $text .= "🚫 Для просмотра Имени авторизуйтесь или зарегистрируйтесь 🚫\n";
            $text .= "🚫 Для просмотра telegram авторизуйтесь или зарегистрируйтесь 🚫\n";
        }

        $text .= "------------------------------\n";
        $text .= "💬 {$post->discription}\n";
        $text .= "Просмотры: {$post->view}";

        return $text;
    }

    /**
     * Начало добавления объявления.
     */
    private function handleAddPost(int $chatId, int $telegramId): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, '❌ Для добавления объявления необходимо авторизоваться или зарегистрироваться.', $this->getGuestKeyboard());

            return;
        }

        // Мужчинам нужен статус проверенного
        if ($user->sex == 1 && $user->prov != 1) {
            $this->sendMessage($chatId, "❌ Мужчинам для подачи объявления необходимо приобрести статус проверенного пользователя.\n\nДевушки могут подавать объявления бесплатно.", $this->getAuthKeyboard());

            return;
        }

        // Лимит: 1 объявление в сутки
        $lastPost = DB::table('post')
            ->where('email', $user->email)
            ->where('del', 0)
            ->where('date', '>=', now()->subDay())
            ->orderByDesc('date')
            ->first(['date']);

        if ($lastPost) {
            $nextAt = \Carbon\Carbon::parse($lastPost->date)->addDay();
            $hours = (int) now()->diffInHours($nextAt);
            $minutes = (int) now()->diffInMinutes($nextAt) % 60;
            $timeLeft = $hours > 0 ? "{$hours} ч. {$minutes} мин." : "{$minutes} мин.";
            $this->sendMessage($chatId, "⏳ Вы уже добавили объявление сегодня.\n\nСледующее объявление можно подать через <b>{$timeLeft}</b>", $this->getAuthKeyboard());

            return;
        }

        Cache::put("tg_state_{$telegramId}", [
            'step' => 'post_title',
            'post_data' => [],
        ], now()->addMinutes(30));

        $this->sendMessage($chatId, "📝 <b>Добавление объявления</b>\n\nВведите заголовок объявления:\n<i>Например: Ищу партнера для серьезных отношений</i>", $this->getCancelKeyboard());
    }

    /**
     * Обработка шагов создания объявления.
     */
    private function handlePostDialogFlow(int $chatId, int $telegramId, ?string $username, string $text, array $state): void
    {
        switch ($state['step']) {
            case 'post_title':
                $title = trim($text);
                if (mb_strlen($title) > 255) {
                    $this->sendMessage($chatId, '❌ Заголовок слишком длинный (макс. 255 символов). Попробуйте короче:');

                    return;
                }
                if (mb_strlen($title) < 2) {
                    $this->sendMessage($chatId, '❌ Заголовок слишком короткий. Введите хотя бы 2 символа:');

                    return;
                }

                $state['post_data']['title'] = $title;
                $state['step'] = 'post_fio';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendMessage($chatId, '👤 Введите ваше ФИО или Имя:');
                break;

            case 'post_fio':
                $fio = trim($text);
                if (mb_strlen($fio) < 2) {
                    $this->sendMessage($chatId, '❌ Имя слишком короткое. Введите хотя бы 2 символа:');

                    return;
                }

                $state['post_data']['fio'] = $fio;
                $state['step'] = 'post_phone';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendMessage($chatId, "📞 Введите номер телефона:\n<i>Формат: +77001234567</i>", [
                    'inline_keyboard' => [
                        [['text' => '⏩ Пропустить', 'callback_data' => 'post_skip_phone']],
                    ],
                ]);
                break;

            case 'post_phone':
                $state['post_data']['phone'] = trim($text);
                $state['step'] = 'post_city';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendCityInlineKeyboard($chatId);
                break;

            case 'post_whats':
                $state['post_data']['whats'] = trim($text);
                $state['step'] = 'post_telegram';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendMessage($chatId, "💬 Введите ваш Telegram:\n<i>Формат: @username</i>", [
                    'inline_keyboard' => [
                        [['text' => '⏩ Пропустить', 'callback_data' => 'post_skip_telegram']],
                    ],
                ]);
                break;

            case 'post_telegram':
                $state['post_data']['telegram'] = trim($text);
                $state['step'] = 'post_description';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendMessage($chatId, "💬 Введите описание объявления:\n<i>Расскажите о себе и о том, кого ищете...</i>");
                break;

            case 'post_description':
                $desc = trim($text);
                if (mb_strlen($desc) < 10) {
                    $this->sendMessage($chatId, '❌ Описание слишком короткое. Напишите хотя бы 10 символов:');

                    return;
                }

                $state['post_data']['discription'] = $desc;
                $state['step'] = 'post_confirm';
                Cache::put("tg_state_{$telegramId}", $state, now()->addMinutes(30));

                $this->sendPostPreview($chatId, $telegramId, $state['post_data']);
                break;
        }
    }

    /**
     * Inline-кнопки выбора города для объявления.
     */
    private function sendCityInlineKeyboard(int $chatId): void
    {
        $cities = DB::table('city')->orderBy('id')->get();
        $rows = [];
        $row = [];

        foreach ($cities as $city) {
            $row[] = ['text' => $city->title, 'callback_data' => "post_city_{$city->id}"];
            if (count($row) === 2) {
                $rows[] = $row;
                $row = [];
            }
        }
        if (! empty($row)) {
            $rows[] = $row;
        }

        $this->sendMessage($chatId, '🏙 Выберите город:', [
            'inline_keyboard' => $rows,
        ]);
    }

    /**
     * Превью объявления перед публикацией.
     */
    private function sendPostPreview(int $chatId, int $telegramId, array $data): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();
        $cityName = '';
        if (! empty($data['city'])) {
            $city = DB::table('city')->where('id', $data['city'])->first();
            $cityName = $city ? $city->title : '';
        }

        $sexLabel = $user->sex == 1 ? '👦 Мужчина' : '👩 Женщина';
        $whoLabel = $user->sex == 1 ? '👩 Ищу: Женщину' : '👦 Ищу: Мужчину';

        $text = "📋 <b>Проверьте ваше объявление:</b>\n\n";
        $text .= "📌 <b>Заголовок:</b> {$data['title']}\n";
        $text .= "👤 <b>Имя:</b> {$data['fio']}\n";
        $text .= '📞 <b>Телефон:</b> '.($data['phone'] ?? 'Не указан')."\n";
        $text .= "🏙 <b>Город:</b> {$cityName}\n";
        $text .= '📱 <b>WhatsApp:</b> '.($data['whats'] ?? 'Не указан')."\n";
        $text .= '💬 <b>Telegram:</b> '.($data['telegram'] ?? 'Не указан')."\n";
        $text .= "👤 <b>Пол:</b> {$sexLabel}\n";
        $text .= "{$whoLabel}\n\n";
        $text .= "💬 <b>Описание:</b>\n{$data['discription']}\n";

        $this->sendMessage($chatId, $text, [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Опубликовать', 'callback_data' => 'post_publish'],
                    ['text' => '❌ Отменить', 'callback_data' => 'post_cancel'],
                ],
            ],
        ]);
    }

    /**
     * Публикация объявления в БД.
     */
    private function publishPost(int $chatId, int $telegramId, ?string $username): void
    {
        $state = Cache::get("tg_state_{$telegramId}");
        if (! $state || $state['step'] !== 'post_confirm') {
            $this->sendMessage($chatId, '❌ Сессия истекла. Попробуйте заново.');

            return;
        }

        $user = DB::table('users')->where('telegram_id', $telegramId)->first();
        if (! $user) {
            $this->sendMessage($chatId, '❌ Пользователь не найден.', $this->getGuestKeyboard());
            Cache::forget("tg_state_{$telegramId}");

            return;
        }

        $data = $state['post_data'];

        $postId = DB::table('post')->insertGetId([
            'title' => $data['title'],
            'email' => $user->email ?? '',
            'email_2' => $user->email ?? '',
            'phone' => $data['phone'] ?? '',
            'photo_view' => 0,
            'whats' => $data['whats'] ?? '',
            'telegram' => $data['telegram'] ?? '',
            'date' => now(),
            'fio' => $data['fio'],
            'price' => '0',
            'sex' => $user->sex,
            'who' => $user->sex,
            'country' => '1',
            'city' => $data['city'],
            'discription' => $data['discription'],
            'ip' => '0.0.0.0',
            'del' => 0,
            'from_telegram' => 1,
            'telegram_id' => (string) $telegramId,
            'telegram_username' => $username ?? '',
        ]);

        // AI-модерация временно отключена — модерируем вручную через /moderation-secret
        // $ai = AiModerationService::moderate($data['title'], $data['discription']);

        Cache::forget("tg_state_{$telegramId}");

        $this->sendMessage($chatId, '✅ Ваше объявление успешно опубликовано!', $this->getAuthKeyboard());
    }

    /**
     * Отправка заявки на покупку статуса админу.
     */
    private function sendBuyStatusHelp(int $chatId, int $telegramId, string $text): void
    {
        $tgContact = trim($text);
        if (empty($tgContact)) {
            $this->sendMessage($chatId, '❌ Введите ваш Telegram для связи:');

            return;
        }

        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        $adminMessage = "⭐ <b>Заявка на статус проверенного пользователя</b>\n\n";
        $adminMessage .= '👤 <b>Имя:</b> '.($user->fio ?? 'Не указано')."\n";
        $adminMessage .= '📧 <b>Email:</b> '.($user->email ?? 'Не указано')."\n";
        $adminMessage .= "💬 <b>Telegram:</b> {$tgContact}\n";
        $adminMessage .= "🆔 <b>Telegram ID:</b> {$telegramId}\n";

        // Отправляем админу через бота уведомлений
        $notifyToken = config('services.telegram.bot_token');
        $notifyChatId = config('services.telegram.chat_id');

        if ($notifyToken && $notifyChatId) {
            Http::post("https://api.telegram.org/bot{$notifyToken}/sendMessage", [
                'chat_id' => $notifyChatId,
                'text' => $adminMessage,
                'parse_mode' => 'HTML',
            ]);
        }

        Cache::forget("tg_state_{$telegramId}");

        $this->sendMessage($chatId, "✅ Ваша заявка отправлена!\n\nАдмин свяжется с вами в ближайшее время.", $this->getAuthKeyboard());
    }

    /**
     * Отправка сообщения админу.
     */
    private function sendAdminMessage(int $chatId, int $telegramId, string $text): void
    {
        $message = trim($text);
        if (empty($message)) {
            $this->sendMessage($chatId, '❌ Сообщение не может быть пустым. Напишите ваше сообщение:');

            return;
        }

        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        $username = $user->telegram_username ?? null;
        $tgLink = $username ? "@{$username}" : "ID: {$telegramId}";

        $adminMessage = "🔔 <b>Новое сообщение из Telegram бота</b>\n\n";
        $adminMessage .= '📧 <b>Email:</b> ' . ($user->email ?? 'Не указано') . "\n";
        $adminMessage .= "💬 <b>Telegram:</b> {$tgLink}\n";
        $adminMessage .= "🆔 <b>Telegram ID:</b> {$telegramId}\n";
        $adminMessage .= "\n💌 <b>Сообщение:</b>\n{$message}";

        $notifyToken = config('services.telegram.bot_token');
        $notifyChatId = config('services.telegram.chat_id');

        $sent = false;
        if ($notifyToken && $notifyChatId) {
            $response = Http::post("https://api.telegram.org/bot{$notifyToken}/sendMessage", [
                'chat_id' => $notifyChatId,
                'text' => $adminMessage,
                'parse_mode' => 'HTML',
            ]);
            $sent = $response->successful();
        }

        Cache::forget("tg_state_{$telegramId}");

        if ($sent) {
            $this->sendMessage($chatId, "✅ Сообщение отправлено!\n\nАдмин свяжется с вами в ближайшее время.", $this->getAuthKeyboard());
        } else {
            $this->sendMessage($chatId, '❌ Ошибка отправки. Попробуйте позже.', $this->getAuthKeyboard());
        }
    }

    /**
     * Поднять объявление в ТОП.
     */
    private function handleBoostTop(int $chatId, int $telegramId, int $postId): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, '❌ Для этого необходимо авторизоваться.', $this->getGuestKeyboard());

            return;
        }

        // Проверяем что объявление принадлежит пользователю
        $post = DB::table('post')->where('id', $postId)->where('del', 0)->first();
        if (! $post || ($post->email !== $user->email && (string) $post->telegram_id !== (string) $telegramId)) {
            $this->sendMessage($chatId, '❌ Объявление не найдено.', $this->getAuthKeyboard());

            return;
        }

        // Проверяем, не в ТОПе ли уже
        $existingTop = DB::table('top_post')
            ->where('id_post', $postId)
            ->where('date_end', '>=', now())
            ->first();

        if ($existingTop) {
            $dateEnd = date('d.m.Y', strtotime($existingTop->date_end));
            $this->sendMessage($chatId, "⭐ Это объявление уже в ТОПе!\n📅 Действует до: <b>{$dateEnd}</b>", $this->getAuthKeyboard());

            return;
        }

        // Генерируем автологин-ссылку на страницу оплаты ТОП
        $token = bin2hex(random_bytes(32));
        Cache::put("auto_login_{$token}", [
            'user_id' => $user->id,
            'redirect' => "/boost-top?post_id={$postId}",
        ], now()->addMinutes(30));

        $siteUrl = rtrim(config('app.url', 'https://goldkazyna.kz'), '/');

        $text = "🚀 <b>Поднять в ТОП: «{$post->title}»</b>\n\n";
        $text .= "Ваше объявление будет показываться первым на главной странице.\n\n";
        $text .= "💰 <b>Тарифы:</b>\n\n";
        $text .= "🔹 <b>5 дней</b> — 7 592 ₸ (~\$14)\n";
        $text .= "🔹 <b>10 дней</b> — 10 846 ₸ (~\$20)\n";
        $text .= "🔹 <b>30 дней</b> — 16 268 ₸ (~\$30) ⭐ Популярный\n\n";
        $text .= 'Нажмите кнопку — вы автоматически войдёте на сайт и попадёте на страницу оплаты:';

        $this->sendMessage($chatId, $text, [
            'inline_keyboard' => [
                [['text' => '💳 Перейти к оплате', 'url' => "{$siteUrl}/auto-login/{$token}"]],
            ],
        ]);
    }

    /**
     * Купить статус проверенного пользователя.
     */
    private function handleBuyStatus(int $chatId, int $telegramId): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, '❌ Для покупки статуса необходимо авторизоваться или зарегистрироваться.', $this->getGuestKeyboard());

            return;
        }

        if ($user->prov == 1) {
            $provDate = $user->prov_date ? date('d.m.Y', strtotime($user->prov_date)) : '';
            $text = '✅ У вас уже куплен статус проверенного пользователя!';
            if ($provDate) {
                $text .= "\n📅 Действует до: <b>{$provDate}</b>";
            }
            $this->sendMessage($chatId, $text, $this->getAuthKeyboard());

            return;
        }

        // Генерируем одноразовый токен для автологина
        $token = bin2hex(random_bytes(32));
        Cache::put("auto_login_{$token}", [
            'user_id' => $user->id,
            'redirect' => '/become-verified',
        ], now()->addMinutes(30));

        $siteUrl = rtrim(config('app.url', 'https://goldkazyna.kz'), '/');

        $text = "📌 <b>Статус проверенного пользователя</b>\n\n";
        $text .= "Со статусом вы получаете:\n";
        $text .= "✅ Значок проверенного\n";
        $text .= "✅ Повышенное доверие\n";
        $text .= "✅ Больше откликов\n\n";
        $text .= "💰 <b>Тарифы:</b>\n\n";
        $text .= "🔹 <b>5 дней</b> — 7 592 ₸ (~\$14)\n";
        $text .= "🔹 <b>10 дней</b> — 10 846 ₸ (~\$20)\n";
        $text .= "🔹 <b>30 дней</b> — 16 268 ₸ (~\$30) ⭐ Популярный\n\n";
        $text .= 'Нажмите кнопку — вы автоматически войдёте на сайт и попадёте на страницу оплаты:';

        $this->sendMessage($chatId, $text, [
            'inline_keyboard' => [
                [['text' => '💳 Перейти к оплате', 'url' => "{$siteUrl}/auto-login/{$token}"]],
                [['text' => '💬 Написать админу для помощи', 'callback_data' => 'buy_status_help']],
            ],
        ]);
    }

    /**
     * Мои объявления с пагинацией.
     */
    private function handleMyPosts(int $chatId, int $telegramId, int $page = 1): void
    {
        $user = DB::table('users')->where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, '❌ Для просмотра объявлений необходимо авторизоваться.', $this->getGuestKeyboard());

            return;
        }

        $perPage = 5;

        $baseQuery = DB::table('post')
            ->where('post.del', 0)
            ->where(function ($q) use ($user, $telegramId) {
                $q->where('post.email', $user->email)
                    ->orWhere('post.telegram_id', (string) $telegramId);
            });

        $total = (clone $baseQuery)->count();

        if ($total === 0) {
            $this->sendMessage($chatId, '📭 У вас пока нет объявлений.', $this->getAuthKeyboard());

            return;
        }

        $totalPages = max(1, ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $posts = (clone $baseQuery)
            ->leftJoin('city', 'post.city', '=', 'city.id')
            ->select('post.*', 'city.title as city_name')
            ->orderByDesc('post.date')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Удаляем старые сообщения предыдущей страницы
        $oldMessages = Cache::get("tg_myposts_msgs_{$telegramId}", []);
        if (! empty($oldMessages)) {
            $this->deleteMessages($chatId, $oldMessages);
            Cache::forget("tg_myposts_msgs_{$telegramId}");
        }

        $sentMessages = [];

        $sentMessages[] = $this->sendMessage($chatId, "📨 <b>Ваши объявления ({$total}):</b>");

        foreach ($posts as $post) {
            $text = $this->formatPost($post, $user);

            $sentMessages[] = $this->sendMessage($chatId, $text, [
                'inline_keyboard' => [
                    [
                        ['text' => '🚀 Поднять в ТОП', 'callback_data' => "mypost_top_{$post->id}"],
                        ['text' => '🗑 Удалить', 'callback_data' => "mypost_del_{$post->id}"],
                    ],
                ],
            ]);
        }

        // Кнопки пагинации
        $buttons = [];
        if ($page > 1) {
            $buttons[] = ['text' => '◀ Назад', 'callback_data' => 'myposts_page_'.($page - 1)];
        }
        if ($page < $totalPages) {
            $buttons[] = ['text' => '▶ Далее', 'callback_data' => 'myposts_page_'.($page + 1)];
        }

        $inline = [];
        if (! empty($buttons)) {
            $inline[] = $buttons;
        }
        $inline[] = [['text' => '🔙 В главное меню', 'callback_data' => 'back_to_menu']];

        $sentMessages[] = $this->sendMessage($chatId, "📄 Страница: {$page}/{$totalPages}", [
            'inline_keyboard' => $inline,
        ]);

        // Сохраняем ID сообщений для удаления при переключении страницы
        Cache::put("tg_myposts_msgs_{$telegramId}", array_filter($sentMessages), now()->addMinutes(30));
    }

    /**
     * Отправка сообщения через Telegram Bot API.
     */
    private function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): ?int
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::post("{$this->apiUrl}/sendMessage", $params);

        return $response->json('result.message_id');
    }

    /**
     * Удаление сообщений по массиву ID.
     */
    private function deleteMessages(int $chatId, array $messageIds): void
    {
        foreach ($messageIds as $msgId) {
            Http::post("{$this->apiUrl}/deleteMessage", [
                'chat_id' => $chatId,
                'message_id' => $msgId,
            ]);
        }
    }

    /**
     * Клавиатура для неавторизованного пользователя.
     */
    private function getGuestKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '🖊 Авторизоваться'], ['text' => '💾 Зарегистрироваться']],
                [['text' => '🔍 Просмотреть объявления'], ['text' => '➕ Добавить объявление']],
                [['text' => '💻 Написать админу'], ['text' => '📄 Инструкция']],
                [['text' => '🚀 Топ объявлений']],
            ],
            'resize_keyboard' => true,
            'input_field_placeholder' => 'Выберите действие',
        ];
    }

    /**
     * Клавиатура для авторизованного пользователя.
     */
    private function getAuthKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '🔍 Просмотреть объявления'], ['text' => '➕ Добавить объявление']],
                [['text' => '📨 Мои объявления'], ['text' => '📌 Купить статус']],
                [['text' => '💻 Написать админу'], ['text' => '📄 Инструкция']],
                [['text' => '🚀 Топ объявления'], ['text' => '❌ Выход']],
            ],
            'resize_keyboard' => true,
            'input_field_placeholder' => 'Выберите действие',
        ];
    }

    /**
     * Клавиатура городов.
     */
    private function getCityKeyboard(): array
    {
        $cities = DB::table('city')->orderBy('id')->get();
        $keyboard = [[['text' => '🏠 Главное меню']]];
        $row = [];

        foreach ($cities as $city) {
            $row[] = ['text' => $city->title];
            if (count($row) === 2) {
                $keyboard[] = $row;
                $row = [];
            }
        }
        if (! empty($row)) {
            $keyboard[] = $row;
        }

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'input_field_placeholder' => 'Выберите город',
        ];
    }

    /**
     * Клавиатура отмены.
     */
    private function getCancelKeyboard(): array
    {
        return [
            'keyboard' => [
                [['text' => '❌ Отмена']],
            ],
            'resize_keyboard' => true,
        ];
    }
}
