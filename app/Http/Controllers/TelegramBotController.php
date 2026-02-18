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

        // Проверяем состояние диалога (авторизация)
        $state = Cache::get("tg_state_{$telegramId}");

        if ($state) {
            $this->handleAuthFlow($chatId, $telegramId, $username, $text, $state);
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
     * Процесс авторизации по шагам.
     */
    private function handleAuthFlow(int $chatId, int $telegramId, ?string $username, string $text, array $state): void
    {
        // Отмена на любом шаге
        if ($text === '❌ Отмена') {
            Cache::forget("tg_state_{$telegramId}");
            $this->sendMessage($chatId, "Авторизация отменена.", $this->getGuestKeyboard());
            return;
        }

        switch ($state['step']) {
            case 'awaiting_email':
                $email = trim(mb_strtolower($text));

                $user = DB::table('users')->where('email', $email)->first();

                if (!$user) {
                    $this->sendMessage($chatId, "❌ Пользователь с таким email не найден.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");
                    return;
                }

                Cache::put("tg_state_{$telegramId}", [
                    'step' => 'awaiting_password',
                    'email' => $email,
                    'user_id' => $user->id,
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, "🔑 Введите пароль:");
                break;

            case 'awaiting_password':
                $password = $text;
                $userId = $state['user_id'];

                $user = DB::table('users')->where('id', $userId)->first();

                if (!$user || $user->password !== sha1(md5($password))) {
                    $this->sendMessage($chatId, "❌ Неверный пароль.\n\nПопробуйте ещё раз или нажмите «❌ Отмена»:");
                    return;
                }

                // Сохраняем telegram_id и username
                DB::table('users')->where('id', $userId)->update([
                    'telegram_id' => $telegramId,
                    'telegram_username' => $username,
                ]);

                Cache::forget("tg_state_{$telegramId}");

                $name = $user->name ?? 'пользователь';
                $this->sendMessage($chatId, "✅ Вы успешно авторизованы, {$name}!", $this->getAuthKeyboard());
                break;
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
                    'step' => 'awaiting_email',
                ], now()->addMinutes(10));

                $this->sendMessage($chatId, "📧 Введите ваш email:", $this->getCancelKeyboard());
                break;

            case '💾 Зарегистрироваться':
                $this->sendMessage($chatId, "Функция регистрации будет доступна в следующем обновлении.");
                break;

            case '🔍 Просмотреть объявления':
                $this->sendMessage($chatId, "Функция просмотра объявлений будет доступна в следующем обновлении.");
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
                if ($user) {
                    DB::table('users')->where('id', $user->id)->update([
                        'telegram_id' => null,
                        'telegram_username' => null,
                    ]);
                }
                $this->sendMessage($chatId, "👋 Вы вышли из аккаунта.", $this->getGuestKeyboard());
                break;

            default:
                $keyboard = $user ? $this->getAuthKeyboard() : $this->getGuestKeyboard();
                $this->sendMessage($chatId, "Неизвестная команда. Выберите действие из меню:", $keyboard);
                break;
        }
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
     * Клавиатура отмены (во время авторизации).
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
