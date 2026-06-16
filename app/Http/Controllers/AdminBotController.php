<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Отдельный админ-бот для РУЧНОЙ выдачи статуса «Проверенный пользователь»
 * и поднятия объявления в ТОП — без онлайн-оплаты.
 *
 * Доступ только для Telegram ID из TELEGRAM_ADMIN_IDS.
 * Логика активации скопирована 1-в-1 из PaymentController::callback().
 */
class AdminBotController extends Controller
{
    private string $botToken;

    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = (string) config('services.telegram.bot_token_admin');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Удаление webhook — перед переходом на long polling.
     */
    public function disableWebhook(Request $request)
    {
        $response = $this->telegramHttp()->post("{$this->apiUrl}/deleteWebhook", [
            'drop_pending_updates' => false,
        ]);

        return response()->json($response->json());
    }

    /**
     * Long polling: дёргается web-cron'ом каждую минуту.
     * Свой offset и lock, чтобы не конфликтовать с основным ботом.
     */
    public function pollUpdates(Request $request)
    {
        $lock = Cache::lock('admin_bot_poll', 60);

        if (! $lock->get()) {
            return response()->json(['ok' => true, 'skipped' => 'lock held']);
        }

        @set_time_limit(60);

        $startTime = time();
        $maxRunTime = 50;
        $longPollTimeout = 25;
        $totalProcessed = 0;
        $lastId = 0;

        try {
            while (time() - $startTime < $maxRunTime) {
                $offset = (int) Cache::get('admin_bot_poll_offset', 0);
                $remaining = $maxRunTime - (time() - $startTime);
                $tgTimeout = max(1, min($longPollTimeout, $remaining - 3));

                $response = $this->telegramHttp()
                    ->timeout($tgTimeout + 5)
                    ->post("{$this->apiUrl}/getUpdates", [
                        'offset' => $offset,
                        'timeout' => $tgTimeout,
                        'allowed_updates' => json_encode(['message', 'callback_query']),
                    ]);

                $body = $response->json();

                if (! ($body['ok'] ?? false)) {
                    Log::channel('telegram')->error('Admin getUpdates failed', $body ?? ['raw' => $response->body()]);
                    break;
                }

                $updates = $body['result'] ?? [];

                foreach ($updates as $update) {
                    Log::channel('telegram')->info('Admin polled update', $update);

                    try {
                        $this->processUpdate($update);
                    } catch (\Throwable $e) {
                        Log::channel('telegram')->error('Admin bot error', [
                            'message' => $e->getMessage(),
                            'file' => $e->getFile().':'.$e->getLine(),
                            'update' => $update,
                        ]);

                        $chatId = $update['callback_query']['message']['chat']['id']
                            ?? $update['message']['chat']['id']
                            ?? null;

                        if ($chatId) {
                            $this->sendMessage($chatId, '❌ Произошла ошибка. Нажмите /start');
                        }
                    }

                    $lastId = $update['update_id'];
                    $totalProcessed++;
                }

                if (! empty($updates)) {
                    Cache::forever('admin_bot_poll_offset', $lastId + 1);
                }
            }

            return response()->json([
                'ok' => true,
                'processed' => $totalProcessed,
                'duration' => time() - $startTime,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Прокси/Telegram недоступны — не валим cron в 500, пишем понятную строку.
            Log::channel('telegram')->error('Admin bot: Telegram unreachable (poll) — proxy down?', [
                'message' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'error' => 'telegram_unreachable', 'detail' => $e->getMessage()]);
        } finally {
            $lock->release();
        }
    }

    /**
     * Маршрутизация апдейта.
     */
    private function processUpdate(array $update): void
    {
        if (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);

            return;
        }

        if (! isset($update['message'])) {
            return;
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $telegramId = $message['from']['id'];
        $text = trim($message['text'] ?? '');

        // Доступ только для админов. Остальным показываем их ID — чтобы
        // можно было вписать его в TELEGRAM_ADMIN_IDS при первичной настройке.
        if (! $this->isAdmin($telegramId)) {
            $this->sendMessage($chatId, "⛔ Доступ запрещён.\nВаш Telegram ID: <code>{$telegramId}</code>");

            return;
        }

        if ($text === '/start') {
            $this->startFlow($chatId, $telegramId);

            return;
        }

        $state = Cache::get($this->stateKey($telegramId), []);

        if (($state['step'] ?? null) === 'awaiting_email') {
            $this->handleEmail($chatId, $telegramId, $text);

            return;
        }

        $this->startFlow($chatId, $telegramId);
    }

    /**
     * Старт: просим email.
     */
    private function startFlow(int $chatId, int $telegramId): void
    {
        Cache::put($this->stateKey($telegramId), ['step' => 'awaiting_email'], now()->addMinutes(30));
        $this->sendMessage($chatId, '👋 Введите <b>email</b> пользователя:');
    }

    /**
     * Поиск пользователя по email и выбор услуги.
     */
    private function handleEmail(int $chatId, int $telegramId, string $email): void
    {
        $email = mb_strtolower(trim($email));

        $user = DB::table('users')->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $this->sendMessage($chatId, '❌ Пользователь с email <b>'.e($email)."</b> не найден.\nВведите email ещё раз:");

            return;
        }

        Cache::put($this->stateKey($telegramId), [
            'step' => 'choose_service',
            'user_id' => $user->id,
            'email' => $user->email,
            'fio' => $user->fio ?? '',
        ], now()->addMinutes(30));

        $this->sendMessage(
            $chatId,
            '✅ Найден: <b>'.e($user->email)."</b>\nФИО: ".e(($user->fio ?? '') ?: '—')."\n\nЧто выдать?",
            ['inline_keyboard' => [
                [['text' => '✅ Проверенный пользователь', 'callback_data' => 'svc_verified']],
                [['text' => '🚀 Оплата ТОП', 'callback_data' => 'svc_top']],
            ]]
        );
    }

    /**
     * Обработка inline-кнопок.
     */
    private function handleCallback(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'];
        $telegramId = $callback['from']['id'];
        $data = $callback['data'] ?? '';

        $this->answerCallback($callback['id']);

        if (! $this->isAdmin($telegramId)) {
            $this->sendMessage($chatId, "⛔ Доступ запрещён.\nВаш Telegram ID: <code>{$telegramId}</code>");

            return;
        }

        if ($data === 'cancel') {
            Cache::forget($this->stateKey($telegramId));
            $this->sendMessage($chatId, '❌ Отменено. Нажмите /start, чтобы начать заново.');

            return;
        }

        $state = Cache::get($this->stateKey($telegramId), []);

        if (empty($state['user_id'])) {
            $this->sendMessage($chatId, '⌛ Сессия истекла. Нажмите /start.');

            return;
        }

        // Выбор услуги «Проверенный пользователь» → сразу срок.
        if ($data === 'svc_verified') {
            $state['service'] = 'verified';
            Cache::put($this->stateKey($telegramId), $state, now()->addMinutes(30));
            $this->sendDaysKeyboard($chatId);

            return;
        }

        // Выбор услуги «ТОП» → список объявлений пользователя.
        if ($data === 'svc_top') {
            $state['service'] = 'top';
            Cache::put($this->stateKey($telegramId), $state, now()->addMinutes(30));
            $this->showUserPosts($chatId, $state);

            return;
        }

        // Выбор конкретного объявления для ТОП → срок.
        if (str_starts_with($data, 'post_')) {
            $state['post_id'] = (int) substr($data, 5);
            Cache::put($this->stateKey($telegramId), $state, now()->addMinutes(30));
            $this->sendDaysKeyboard($chatId);

            return;
        }

        // Выбор срока → сводка-подтверждение.
        if (str_starts_with($data, 'days_')) {
            $state['days'] = (int) substr($data, 5);
            Cache::put($this->stateKey($telegramId), $state, now()->addMinutes(30));
            $this->showSummary($chatId, $state);

            return;
        }

        if ($data === 'confirm') {
            $this->applyActivation($chatId, $telegramId, $state);

            return;
        }
    }

    /**
     * Кнопка выбора срока (пока только 30 дней).
     */
    private function sendDaysKeyboard(int $chatId): void
    {
        $this->sendMessage($chatId, 'Выберите срок:', ['inline_keyboard' => [
            [['text' => '30 дней', 'callback_data' => 'days_30']],
            [['text' => '❌ Отмена', 'callback_data' => 'cancel']],
        ]]);
    }

    /**
     * Список активных объявлений пользователя (по email).
     */
    private function showUserPosts(int $chatId, array $state): void
    {
        $posts = DB::table('post')
            ->where('email', $state['email'])
            ->where('del', 0)
            ->orderByDesc('date')
            ->limit(20)
            ->get();

        if ($posts->isEmpty()) {
            $this->sendMessage($chatId, '📭 У пользователя нет активных объявлений. Нажмите /start.');

            return;
        }

        $keyboard = [];
        foreach ($posts as $post) {
            $title = mb_substr($post->title ?? ('#'.$post->id), 0, 50);
            $keyboard[] = [['text' => "#{$post->id}: {$title}", 'callback_data' => "post_{$post->id}"]];
        }
        $keyboard[] = [['text' => '❌ Отмена', 'callback_data' => 'cancel']];

        $this->sendMessage($chatId, 'Выберите объявление для поднятия в ТОП:', ['inline_keyboard' => $keyboard]);
    }

    /**
     * Сводка перед применением.
     */
    private function showSummary(int $chatId, array $state): void
    {
        $days = (int) ($state['days'] ?? 0);

        if (($state['service'] ?? '') === 'verified') {
            $user = DB::table('users')->where('id', $state['user_id'])->first();
            $newDate = $this->verifiedNewDate($user, $days);

            $text = "<b>Подтвердите выдачу:</b>\n\n"
                .'👤 '.e($state['email'])."\n"
                .'ФИО: '.e(($state['fio'] ?? '') ?: '—')."\n"
                ."Услуга: ✅ Проверенный пользователь\n"
                ."Срок: {$days} дн.\n"
                .'Действует до: <b>'.$newDate->format('d.m.Y H:i').'</b>';
        } else {
            $post = DB::table('post')->where('id', $state['post_id'])->first();
            $newDate = $this->topNewDate((int) $state['post_id'], $days);
            $title = $post ? mb_substr($post->title ?? ('#'.$post->id), 0, 50) : ('#'.$state['post_id']);

            $text = "<b>Подтвердите поднятие в ТОП:</b>\n\n"
                .'👤 '.e($state['email'])."\n"
                ."Услуга: 🚀 ТОП объявления\n"
                .'Объявление: #'.$state['post_id'].' '.e($title)."\n"
                ."Срок: {$days} дн.\n"
                .'В ТОПе до: <b>'.$newDate->format('d.m.Y H:i').'</b>';
        }

        $this->sendMessage($chatId, $text, ['inline_keyboard' => [
            [['text' => '✅ Подтвердить', 'callback_data' => 'confirm']],
            [['text' => '❌ Отмена', 'callback_data' => 'cancel']],
        ]]);
    }

    /**
     * Применение услуги. Логика 1-в-1 с PaymentController::callback().
     */
    private function applyActivation(int $chatId, int $telegramId, array $state): void
    {
        $days = (int) ($state['days'] ?? 0);
        $service = $state['service'] ?? '';

        if ($days <= 0 || $service === '') {
            Cache::forget($this->stateKey($telegramId));
            $this->sendMessage($chatId, '❌ Сессия истекла. Нажмите /start.');

            return;
        }

        if ($service === 'verified') {
            $user = DB::table('users')->where('id', $state['user_id'])->first();

            if (! $user) {
                Cache::forget($this->stateKey($telegramId));
                $this->sendMessage($chatId, '❌ Пользователь не найден.');

                return;
            }

            $newProvDate = $this->verifiedNewDate($user, $days);

            DB::table('users')->where('id', $user->id)->update([
                'prov' => 1,
                'prov_date' => $newProvDate,
            ]);

            Log::channel('telegram')->info('Admin bot: verified activated', [
                'admin' => $telegramId,
                'user_id' => $user->id,
                'days' => $days,
                'prov_date' => $newProvDate->toDateTimeString(),
            ]);

            $this->sendMessage(
                $chatId,
                "✅ <b>Статус выдан!</b>\n".e($user->email).
                "\nДействует до: <b>".$newProvDate->format('d.m.Y H:i').'</b>'.
                "\n\n/start — выдать ещё"
            );
        } elseif ($service === 'top') {
            $postId = (int) ($state['post_id'] ?? 0);

            if ($postId <= 0) {
                Cache::forget($this->stateKey($telegramId));
                $this->sendMessage($chatId, '❌ Объявление не выбрано. Нажмите /start.');

                return;
            }

            // Сбрасываем count_view у всех топ-объявлений (как при оплате).
            DB::table('top_post')->update(['count_view' => 0]);

            $existing = DB::table('top_post')->where('id_post', $postId)->first();

            if ($existing) {
                $baseDate = Carbon::parse($existing->date_end)->isFuture()
                    ? Carbon::parse($existing->date_end)
                    : Carbon::now();
                $dateEnd = $baseDate->copy()->addDays($days);

                DB::table('top_post')->where('id_post', $postId)->update([
                    'date_end' => $dateEnd,
                    'count_view' => 0,
                ]);
            } else {
                $dateEnd = Carbon::now()->addDays($days);

                DB::table('top_post')->insert([
                    'id_post' => $postId,
                    'date' => now(),
                    'date_end' => $dateEnd,
                    'count_view' => 0,
                ]);
            }

            Log::channel('telegram')->info('Admin bot: top activated', [
                'admin' => $telegramId,
                'post_id' => $postId,
                'days' => $days,
                'date_end' => $dateEnd->toDateTimeString(),
            ]);

            $this->sendMessage(
                $chatId,
                "✅ <b>Поднято в ТОП!</b>\nОбъявление #{$postId}".
                "\nВ ТОПе до: <b>".$dateEnd->format('d.m.Y H:i').'</b>'.
                "\n\n/start — поднять ещё"
            );
        }

        Cache::forget($this->stateKey($telegramId));
    }

    /**
     * Новая дата статуса: если статус ещё активен — продлеваем от него, иначе от сейчас.
     */
    private function verifiedNewDate(?object $user, int $days): Carbon
    {
        $baseDate = ($user && $user->prov == 1 && $user->prov_date && Carbon::parse($user->prov_date)->isFuture())
            ? Carbon::parse($user->prov_date)
            : Carbon::now();

        return $baseDate->copy()->addDays($days);
    }

    /**
     * Новая дата ТОП: если объявление уже в ТОПе и срок не вышел — продлеваем, иначе от сейчас.
     */
    private function topNewDate(int $postId, int $days): Carbon
    {
        $existing = DB::table('top_post')->where('id_post', $postId)->first();

        $baseDate = ($existing && Carbon::parse($existing->date_end)->isFuture())
            ? Carbon::parse($existing->date_end)
            : Carbon::now();

        return $baseDate->copy()->addDays($days);
    }

    private function isAdmin(int $telegramId): bool
    {
        $raw = (string) config('services.telegram.admin_ids');
        $ids = array_filter(array_map('trim', explode(',', $raw)), fn ($v) => $v !== '');

        return in_array((string) $telegramId, $ids, true);
    }

    private function stateKey(int $telegramId): string
    {
        return "admin_bot_state_{$telegramId}";
    }

    private function answerCallback(string $callbackId): void
    {
        $this->telegramHttp()->post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callbackId,
        ]);
    }

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

        $response = $this->telegramHttp()->post("{$this->apiUrl}/sendMessage", $params);

        return $response->json('result.message_id');
    }

    private function telegramHttp(): \Illuminate\Http\Client\PendingRequest
    {
        $proxy = config('services.telegram.proxy');

        $client = Http::asForm()
            ->connectTimeout(5)
            ->timeout(10);

        if ($proxy) {
            $client = $client->withOptions(['proxy' => $proxy]);
        }

        return $client;
    }
}
