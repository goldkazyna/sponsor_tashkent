<?php

namespace App\Http\Controllers;

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

        if (!isset($update['message'])) {
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
        $this->sendMessage($chatId, "Добро пожаловать! Выберите действие:", $this->getGuestKeyboard());
    }

    /**
     * Процесс авторизации/регистрации по шагам.
     */
    private function handleDialogFlow(int $chatId, int $telegramId, ?string $username, string $text, array $state): void
    {
        // Отмена на любом шаге
        if ($text === '❌ Отмена') {
            Cache::forget("tg_state_{$telegramId}");
            $this->sendMessage($chatId, "Действие отменено.", $this->getGuestKeyboard());
            return;
        }

        switch ($state['step']) {
            // === АВТОРИЗАЦИЯ ===
            case 'login_email':
                $email = trim(mb_strtolower($text));
                $user = DB::table('users')->where('email', $email)->first();

                if (!$user) {
                    $this->sendMessage($chatId, "❌ Пользователь с таким email не найден.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");
                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'login_password',
                    'user_id' => $user->id,
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, "🔑 Введите пароль:");
                break;

            case 'login_password':
                $user = DB::table('users')->where('id', $state['user_id'])->first();

                if (!$user || $user->password !== sha1(md5($text))) {
                    $this->sendMessage($chatId, "❌ Неверный пароль.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");
                    return;
                }

                DB::table('users')->where('id', $user->id)->update([
                    'telegram_id' => $telegramId,
                    'telegram_username' => $username,
                ]);

                Cache::forget("tg_state_{$telegramId}");
                $this->sendMessage($chatId, "✅ Вы успешно авторизованы!", $this->getAuthKeyboard());
                break;

            // === РЕГИСТРАЦИЯ ===
            case 'register_email':
                $email = trim(mb_strtolower($text));

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

                $this->sendMessage($chatId, "👤 Выберите ваш пол:", [
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

        // Возврат в главное меню
        if ($data === 'back_to_menu') {
            $user = DB::table('users')->where('telegram_id', $telegramId)->first();
            $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
            $this->sendMessage($chatId, "📋 Главное меню:", $keyboard);
            return;
        }

        if (in_array($data, ['sex_1', 'sex_2'])) {
            $state = Cache::get("tg_state_{$telegramId}");

            if (!$state || $state['step'] !== 'register_sex') {
                $this->sendMessage($chatId, "❌ Сессия истекла. Начните регистрацию заново.", $this->getGuestKeyboard());
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
                . "📧 Email: <b>{$email}</b>\n"
                . "🔑 Пароль: <code>{$password}</code>\n"
                . "👤 Пол: {$sexLabel}\n\n"
                . "Сохраните пароль — он нужен для входа на сайте.",
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
                    $this->sendMessage($chatId, "Вы уже авторизованы!", $this->getAuthKeyboard());
                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'login_email',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, "📧 Введите ваш email:", $this->getCancelKeyboard());
                break;

            case '💾 Зарегистрироваться':
                if ($user) {
                    $this->sendMessage($chatId, "Вы уже зарегистрированы!", $this->getAuthKeyboard());
                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'register_email',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, "📧 Введите ваш email:", $this->getCancelKeyboard());
                break;

            case '🔍 Просмотреть объявления':
                $this->sendMessage($chatId, "🏙 Выберите город или смотрите все объявления:", $this->getCityKeyboard());
                $this->handleViewPosts($chatId, $user, 1);
                break;

            case '➕ Добавить объявление':
                $this->sendMessage($chatId, "Функция добавления объявлений будет доступна в следующем обновлении.");
                break;

            case '💻 Написать админу':
                $this->sendMessage($chatId, "Функция связи с админом будет доступна в следующем обновлении.");
                break;

            case '📄 Инструкция':
                $this->sendMessage($chatId, "Инструкция будет доступна в следующем обновлении.");
                break;

            case '🚀 Топ объявлений':
            case '🚀 Топ объявления':
                $this->sendMessage($chatId, "Топ объявлений будет доступен в следующем обновлении.");
                break;

            case '📨 Мои объявления':
                $this->sendMessage($chatId, "Функция просмотра ваших объявлений будет доступна в следующем обновлении.");
                break;

            case '📌 Купить статус':
                $this->sendMessage($chatId, "Функция покупки статуса будет доступна в следующем обновлении.");
                break;

            case '❌ Выход':
                $this->sendMessage($chatId, "👋 Вы вышли из аккаунта.", $this->getGuestKeyboard());
                break;

            case '🏠 Главное меню':
                $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
                $this->sendMessage($chatId, "📋 Главное меню:", $keyboard);
                break;

            default:
                // Проверяем, не город ли это
                $city = DB::table('city')->where('title', $text)->first();
                if ($city) {
                    $this->handleViewPosts($chatId, $user, 1, $city->id);
                    break;
                }

                $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
                $this->sendMessage($chatId, "Неизвестная команда. Выберите действие из меню:", $keyboard);
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
            $this->sendMessage($chatId, "📭 Объявлений в этом городе пока нет.");
            return;
        }

        foreach ($posts as $post) {
            $this->sendMessage($chatId, $this->formatPost($post, $user));
        }

        // Кнопки пагинации
        $cityParam = $cityId > 0 ? "_{$cityId}" : "_0";
        $buttons = [];
        if ($page > 1) {
            $buttons[] = ['text' => '◀ Предыдущая страница', 'callback_data' => "posts_page_" . ($page - 1) . $cityParam];
        }
        if ($page < $totalPages) {
            $buttons[] = ['text' => '▶ Следующая страница', 'callback_data' => "posts_page_" . ($page + 1) . $cityParam];
        }

        $inline = [];
        if (!empty($buttons)) {
            $inline[] = $buttons;
        }
        $inline[] = [['text' => '🔙 В главное меню', 'callback_data' => 'back_to_menu']];

        $this->sendMessage($chatId, "📄 Страница: {$page}/{$totalPages}", [
            'inline_keyboard' => $inline,
        ]);
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
            if ($tg !== 'Не указан' && !str_starts_with($tg, '@')) {
                $tg = '@' . $tg;
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
     * Отправка сообщения через Telegram Bot API.
     */
    private function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): void
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post("{$this->apiUrl}/sendMessage", $params);
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
        if (!empty($row)) {
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
