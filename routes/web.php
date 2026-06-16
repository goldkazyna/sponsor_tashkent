<?php

use App\Http\Controllers\AdminBotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TelegramBotController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Карта сайта
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Главная страница со списком объявлений
Route::get('/', [PostController::class, 'index'])->name('home');

// Редирект старых URL
Route::get('/soderganki', function () {
    return redirect('/', 301);
});

// Правила сайта
Route::get('/rules', function () {
    return view('rules');
})->name('rules');

// Продажа сервиса
Route::get('/for-sale', function () {
    // Счётчик посещений (файловый, переживает чистку кеша и деплой)
    try {
        $file = storage_path('app/for-sale-stats.json');
        $stats = is_file($file)
            ? (json_decode(file_get_contents($file), true) ?: ['total' => 0, 'days' => []])
            : ['total' => 0, 'days' => []];

        $today = now()->format('Y-m-d');
        $stats['total'] = ($stats['total'] ?? 0) + 1;
        $stats['days'][$today] = ($stats['days'][$today] ?? 0) + 1;

        file_put_contents($file, json_encode($stats), LOCK_EX);
    } catch (\Throwable $e) {
        // Счётчик не должен ломать саму страницу
    }

    return view('for-sale');
})->name('for-sale');

// Статистика посещений страницы продажи (секретный роут)
Route::get('/for-sale-stats-secret', function () {
    $file = storage_path('app/for-sale-stats.json');
    $stats = is_file($file)
        ? (json_decode(file_get_contents($file), true) ?: ['total' => 0, 'days' => []])
        : ['total' => 0, 'days' => []];

    $days = $stats['days'] ?? [];
    krsort($days); // сначала свежие даты
    $today = now()->format('Y-m-d');

    return view('for-sale-stats', [
        'total' => $stats['total'] ?? 0,
        'today' => $days[$today] ?? 0,
        'days'  => $days,
    ]);
});

// Создание таблицы profiles на проде (нет CLI). Идемпотентно. Открыть один раз.
Route::get('/create-profiles-table-secret', function () {
    try {
        DB::statement("
            CREATE TABLE IF NOT EXISTS profiles (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                birthdate DATE NOT NULL,
                city_id INT NOT NULL,
                photo VARCHAR(255) NULL,
                about TEXT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_profiles_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $exists = DB::getSchemaBuilder()->hasTable('profiles');

        return response('Таблица profiles: '.($exists ? 'OK (создана или уже была)' : 'НЕ создана'), 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    } catch (\Throwable $e) {
        return response('Ошибка: '.$e->getMessage(), 500)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
});

// Лог попыток добавления объявлений (секретный роут) — что люди пытались добавить
Route::get('/add-attempts-secret', function () {
    $file = storage_path('app/add_attempts.jsonl');
    $entries = [];
    if (is_file($file)) {
        foreach (array_filter(array_map('trim', file($file))) as $line) {
            $d = json_decode($line, true);
            if (is_array($d)) {
                $entries[] = $d;
            }
        }
    }

    $total = count($entries);
    $blocked = count(array_filter($entries, fn ($e) => empty($e['allowed'])));

    $entries = array_slice(array_reverse($entries), 0, 500); // новые сверху, максимум 500

    return view('add-attempts', [
        'entries' => $entries,
        'total'   => $total,
        'blocked' => $blocked,
    ]);
});

// Регистрация
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Авторизация
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Выход
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Восстановление пароля
Route::get('/password/reset', [AuthController::class, 'showResetRequest'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{code}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/update', [AuthController::class, 'resetPassword'])->name('password.update');

// Объявления
Route::get('/add', [PostController::class, 'create'])->name('post.create');
Route::post('/add', [PostController::class, 'store'])->name('post.store');
Route::get('/post/detail/{id}', [PostController::class, 'show'])->name('post.detail');

// Личный кабинет (требует авторизации)
Route::middleware(['web'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/posts', [ProfileController::class, 'myPosts'])->name('profile.posts');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/anketa', [ProfileController::class, 'anketa'])->name('profile.anketa');
    Route::post('/profile/anketa', [ProfileController::class, 'updateAnketa'])->name('profile.anketa.update');
    Route::get('/profile/pricing', [ProfileController::class, 'pricing'])->name('profile.pricing');

    // Управление объявлениями
    Route::get('/profile/post/edit/{id}', [ProfileController::class, 'editPost'])->name('profile.post.edit');
    Route::post('/profile/post/update/{id}', [ProfileController::class, 'updatePost'])->name('profile.post.update');
    Route::post('/profile/post/delete/{id}', [ProfileController::class, 'deletePost'])->name('profile.post.delete');
    Route::post('/profile/photo/delete/{id}', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password/update', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/delete', [ProfileController::class, 'deleteAccount'])->name('profile.delete');
    // AJAX методы для сообщений
    Route::get('/profile/messages', [ProfileController::class, 'messages'])->name('profile.messages');
    Route::get('/profile/messages/chat/{id}', [ProfileController::class, 'messagesChat'])->name('profile.messages.chat');
    Route::post('/profile/messages/send', [ProfileController::class, 'sendMessage'])->name('profile.messages.send');
    Route::get('/profile/messages/new/{id}', [ProfileController::class, 'getNewMessages'])->name('profile.messages.new');
    Route::get('/profile/messages/unread-count', [ProfileController::class, 'getUnreadCount'])->name('profile.messages.unread');
});

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::get('/become-verified', [ContactController::class, 'showVerified'])->name('become.verified');
Route::post('/become-verified/send', [ContactController::class, 'sendVerified'])->name('become.verified.send');

Route::get('/boost-top', [ContactController::class, 'showBoostTop'])->name('boost.top');
Route::post('/boost-top/send', [ContactController::class, 'sendBoostTop'])->name('boost.top.send');

// Платёжная система
Route::post('/payment/create', [App\Http\Controllers\PaymentController::class, 'createPayment'])->name('payment.create');
Route::post('/result_url_new', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/success_url', [App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
Route::get('/fail', [App\Http\Controllers\PaymentController::class, 'fail'])->name('payment.fail');

// Очистка кеша (без консоли)
Route::get('/clear-cache-secret', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');

    return 'Cache cleared!';
});

Route::get('/apply-ai-secret', function () {
    $posts = DB::table('post')
        ->where('check', 1)
        ->whereNotNull('title_ai')
        ->where('title_ai', '!=', '')
        ->get(['id', 'title', 'title_ai', 'discription', 'discription_ai']);

    if ($posts->isEmpty()) {
        return 'Нет объявлений для обновления.';
    }

    $result = [];
    foreach ($posts as $post) {
        DB::table('post')->where('id', $post->id)->update([
            'title' => $post->title_ai,
            'discription' => $post->discription_ai ?? '',
            'title_ai' => '',
            'discription_ai' => '',
        ]);
        $result[] = "#{$post->id}: «{$post->title}» → «{$post->title_ai}»";
    }

    return 'Обновлено: '.count($result).'<br>'.implode('<br>', $result);
});

// Telegram Bot
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook']);
Route::get('/telegram/set-webhook', [TelegramBotController::class, 'setWebhook']);
Route::get('/telegram/disable-webhook-secret', [TelegramBotController::class, 'disableWebhook']);
Route::get('/telegram/poll-secret', [TelegramBotController::class, 'pollUpdates']);

// Админ-бот (ручная выдача статуса / ТОП — без онлайн-оплаты)
Route::get('/admin-bot/poll-secret', [AdminBotController::class, 'pollUpdates']);
Route::get('/admin-bot/disable-webhook-secret', [AdminBotController::class, 'disableWebhook']);

// Автологин из Telegram бота (одноразовый токен)
Route::get('/auto-login/{token}', function (string $token, Illuminate\Http\Request $request) {
    $data = Illuminate\Support\Facades\Cache::pull("auto_login_{$token}");

    if (! $data || ! isset($data['user_id'], $data['redirect'])) {
        return redirect('/login')->with('error', 'Ссылка недействительна или истекла');
    }

    $user = DB::table('users')->where('id', $data['user_id'])->first();
    if (! $user) {
        return redirect('/login')->with('error', 'Пользователь не найден');
    }

    session(['user_id' => $user->id]);
    session(['user_email' => $user->email]);
    session(['user_sex' => $user->sex]);

    return redirect($data['redirect']);
})->name('auto.login');

// Инструкция по оплате
Route::get('/payment-instruction', function () {
    if (session('user_id')) {
        DB::table('users')->where('id', session('user_id'))->update(['saw_instruction' => 1]);
    }

    return view('payment-instruction');
})->name('payment.instruction');

// Крон: проверка истёкших статусов и ТОП объявлений
Route::get('/cron/check_date', function () {
    $now = now();

    // Снимаем верифицированный статус у пользователей с истёкшей prov_date
    $usersUpdated = DB::table('users')
        ->where('prov', 1)
        ->whereNotNull('prov_date')
        ->where('prov_date', '<', $now)
        ->update(['prov' => 0]);

    // Удаляем истёкшие ТОП объявления
    $topDeleted = DB::table('top_post')
        ->where('date_end', '<', $now)
        ->delete();

    return "OK. Users prov reset: {$usersUpdated}, Top posts removed: {$topDeleted}";
});

// Модерация AI
Route::get('/moderation-secret', function () {
    $post = DB::table('post')
        ->where('del', 0)
        ->where('check', 0)
        ->orderBy('id', 'desc')
        ->first();

    $remaining = DB::table('post')
        ->where('del', 0)
        ->where('check', 0)
        ->count();

    $aiTitle = '';
    $aiDescription = '';
    $aiTelegram = '';
    $aiPhone = '';
    $aiWhatsapp = '';
    $aiDelete = false;
    $aiDeleteReason = '';
    $aiChanges = '';

    if ($post) {
        if (! empty($post->title_ai)) {
            // AI уже обработал раньше
            $aiTitle = $post->title_ai;
            $aiDescription = $post->discription_ai ?? '';
        } else {
            // Прогоняем через AI прямо сейчас
            $result = \App\Services\AiModerationService::moderate(
                $post->title ?? '',
                $post->discription ?? ''
            );
            \Illuminate\Support\Facades\Log::channel('telegram')->info('AiModeration result', $result);
            $aiTitle = $result['title_ai'];
            $aiDescription = $result['discription_ai'];
            $aiTelegram = $result['telegram_extracted'];
            $aiPhone = $result['phone_extracted'];
            $aiWhatsapp = $result['whatsapp_extracted'];
            $aiDelete = $result['delete'];
            $aiDeleteReason = $result['delete_reason'];
            $aiChanges = $result['changes'];
        }
    }

    $rulesFile = storage_path('app/ai_moderation_rules.txt');
    $rules = file_exists($rulesFile) ? array_filter(array_map('trim', file($rulesFile))) : [];

    return view('moderation', compact('post', 'remaining', 'rules', 'aiTitle', 'aiDescription', 'aiTelegram', 'aiPhone', 'aiWhatsapp', 'aiDelete', 'aiDeleteReason', 'aiChanges'));
});

Route::post('/moderation-secret/approve', function (Illuminate\Http\Request $request) {
    $postId = $request->input('post_id');
    $skip = $request->input('skip');
    $delete = $request->input('delete');

    if ($delete) {
        DB::table('post')->where('id', $postId)->update(['del' => 1, 'check' => 1]);

        return redirect('/moderation-secret')->with('success', 'Объявление удалено');
    }

    if (! $skip) {
        $update = ['check' => 1];
        $update['title_ai'] = $request->input('ai_title') ?? '';
        $update['discription_ai'] = $request->input('ai_description') ?? '';

        $aiTelegram = $request->input('ai_telegram') ?? '';
        if ($aiTelegram !== '') {
            $update['telegram'] = $aiTelegram;
        }

        $aiPhone = $request->input('ai_phone') ?? '';
        if ($aiPhone !== '') {
            $update['phone'] = $aiPhone;
        }

        $aiWhatsapp = $request->input('ai_whatsapp') ?? '';
        if ($aiWhatsapp !== '') {
            $update['whats'] = $aiWhatsapp;
        }

        DB::table('post')->where('id', $postId)->update($update);
    } else {
        DB::table('post')->where('id', $postId)->update(['check' => 2]);
    }

    return redirect('/moderation-secret')->with('success', $skip ? 'Пропущено' : 'Проверено, AI-версия сохранена');
});

Route::post('/moderation-secret/add-rule', function (Illuminate\Http\Request $request) {
    $postId = $request->input('post_id');
    $rule = trim($request->input('rule', ''));

    if ($rule !== '') {
        $rulesFile = storage_path('app/ai_moderation_rules.txt');
        file_put_contents($rulesFile, $rule.PHP_EOL, FILE_APPEND);
    }

    DB::table('post')->where('id', $postId)->update(['check' => 2]);

    return redirect('/moderation-secret')->with('success', 'Правило добавлено, объявление пропущено');
});

// Извлечение Telegram-ников из текстов объявлений
Route::get('/extract-telegram-secret', function () {
    $posts = DB::table('post')
        ->where('del', 0)
        ->orderBy('id', 'desc')
        ->limit(30)
        ->get();

    $aiResults = [];
    $debugInfo = '';
    $apiKey = config('services.anthropic.api_key');
    if (empty($apiKey)) {
        $debugInfo = 'ANTHROPIC_API_KEY не задан в .env';
    } elseif ($posts->isNotEmpty()) {
        // Батчим по 10 чтобы не превысить токены
        foreach (array_chunk($posts->all(), 10) as $chunk) {
            $aiResults += \App\Services\AiModerationService::extractTelegram($chunk);
        }
        if (empty($aiResults)) {
            // Показать последние строки из telegram-лога
            $logFile = storage_path('logs/telegram-'.date('Y-m-d').'.log');
            $logTail = '';
            if (file_exists($logFile)) {
                $lines = file($logFile);
                $logTail = implode('', array_slice($lines, -20));
            }
            $debugInfo = 'API вернул пустой результат. Лог:'."\n".$logTail;
        }
    }

    return view('extract-telegram', compact('posts', 'aiResults', 'debugInfo'));
});

Route::post('/extract-telegram-secret/save', function (Illuminate\Http\Request $request) {
    $items = $request->input('items', []);
    $processed = 0;

    foreach ($items as $postId => $telegram) {
        $telegram = trim($telegram);
        if ($telegram === '') {
            continue;
        }

        $post = DB::table('post')->where('id', $postId)->first();
        if (! $post) {
            continue;
        }

        $update = [];

        if (empty($post->telegram)) {
            $update['telegram'] = $telegram;
        }

        $patterns = ['@'.$telegram, $telegram];
        foreach ($patterns as $pattern) {
            $titleField = $update['title'] ?? $post->title ?? '';
            if ($titleField !== '' && stripos($titleField, $pattern) !== false) {
                $update['title'] = trim(preg_replace('/\s{2,}/', ' ', str_ireplace($pattern, '', $titleField)));
            }
            $descField = $update['discription'] ?? $post->discription ?? '';
            if ($descField !== '' && stripos($descField, $pattern) !== false) {
                $update['discription'] = trim(preg_replace('/\s{2,}/', ' ', str_ireplace($pattern, '', $descField)));
            }
            $fioField = $update['fio'] ?? $post->fio ?? '';
            if ($fioField !== '' && stripos($fioField, $pattern) !== false) {
                $update['fio'] = trim(preg_replace('/\s{2,}/', ' ', str_ireplace($pattern, '', $fioField)));
            }
        }

        if (! empty($update)) {
            DB::table('post')->where('id', $postId)->update($update);
            $processed++;
        }
    }

    return redirect('/extract-telegram-secret')->with('success',
        "Обработано объявлений: {$processed}"
    );
});

// Чёрный список Telegram
Route::get('/blacklist-telegram-secret', function () {
    $blacklist = \App\Services\TelegramBlacklistService::getBlacklist();

    return view('blacklist-telegram', compact('blacklist'));
});

Route::post('/blacklist-telegram-secret/add', function (Illuminate\Http\Request $request) {
    $nick = trim($request->input('nick', ''));
    if ($nick !== '') {
        \App\Services\TelegramBlacklistService::addToBlacklist($nick);
    }

    return redirect('/blacklist-telegram-secret')->with('success', "Добавлен: {$nick}");
});

Route::post('/blacklist-telegram-secret/remove', function (Illuminate\Http\Request $request) {
    $nick = trim($request->input('nick', ''));
    if ($nick !== '') {
        \App\Services\TelegramBlacklistService::removeFromBlacklist($nick);
    }

    return redirect('/blacklist-telegram-secret')->with('success', "Удалён: {$nick}");
});

// ── Поиск объявлений с упоминанием несовершеннолетних (16/17 и младше) или девственности ──
// Ничего не удаляет автоматически — только показывает для ручной проверки. Батчи по 50.

if (! function_exists('scanMinorsState')) {
    function scanMinorsStatePath(): string
    {
        return storage_path('app/scan_minors_state.json');
    }

    function scanMinorsResultsPath(): string
    {
        return storage_path('app/scan_minors_results.json');
    }

    function scanMinorsState(): array
    {
        $path = scanMinorsStatePath();
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) {
                return array_merge(['cursor' => null, 'done' => 0], $data);
            }
        }

        return ['cursor' => null, 'done' => 0];
    }

    function scanMinorsSaveState(array $state): void
    {
        file_put_contents(scanMinorsStatePath(), json_encode($state, JSON_UNESCAPED_UNICODE));
    }

    function scanMinorsResults(): array
    {
        $path = scanMinorsResultsPath();
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    function scanMinorsSaveResults(array $results): void
    {
        file_put_contents(scanMinorsResultsPath(), json_encode(array_values($results), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}

Route::get('/scan-minors-secret', function () {
    $state = scanMinorsState();
    $results = scanMinorsResults();

    $total = DB::table('post')->where('del', 0)->count();
    $apiKeySet = ! empty(config('services.anthropic.api_key'));

    return view('scan-minors', [
        'results' => array_reverse($results), // новые сверху
        'done' => $state['done'],
        'total' => $total,
        'cursor' => $state['cursor'],
        'apiKeySet' => $apiKeySet,
    ]);
});

Route::post('/scan-minors-secret/scan', function () {
    @set_time_limit(0);

    $state = scanMinorsState();
    $cursor = $state['cursor'];

    $query = DB::table('post')->where('del', 0);
    if ($cursor !== null) {
        $query->where('id', '<', $cursor);
    }
    $posts = $query->orderBy('id', 'desc')->limit(100)->get();

    if ($posts->isEmpty()) {
        return redirect('/scan-minors-secret')->with('success', 'Готово — все объявления просканированы.');
    }

    // Батчим по 25, чтобы не упереться в токены/таймаут
    $flagged = [];
    foreach (array_chunk($posts->all(), 25) as $chunk) {
        $flagged += \App\Services\AiModerationService::detectMinorsAndVirgin($chunk);
    }

    // Складываем найденное в файл (дедуп по id)
    $results = scanMinorsResults();
    $byId = [];
    foreach ($results as $r) {
        $byId[$r['id']] = $r;
    }

    $foundNow = 0;
    foreach ($posts as $post) {
        if (isset($flagged[$post->id]) && ! isset($byId[$post->id])) {
            $byId[$post->id] = [
                'id' => $post->id,
                'title' => $post->title ?? '',
                'reason' => $flagged[$post->id]['reason'],
                'fragment' => $flagged[$post->id]['fragment'],
            ];
            $foundNow++;
        }
    }

    scanMinorsSaveResults($byId);

    // Двигаем курсор и счётчик
    $minId = $posts->min('id');
    $state['cursor'] = $minId;
    $state['done'] = ($state['done'] ?? 0) + $posts->count();
    scanMinorsSaveState($state);

    return redirect('/scan-minors-secret')->with('success',
        "Просканировано +{$posts->count()}. Найдено в этом батче: {$foundNow}."
    );
});

Route::post('/scan-minors-secret/delete', function (Illuminate\Http\Request $request) {
    $postId = (int) $request->input('post_id');

    if ($postId > 0) {
        DB::table('post')->where('id', $postId)->update(['del' => 1]);

        $results = scanMinorsResults();
        $results = array_filter($results, fn ($r) => (int) $r['id'] !== $postId);
        scanMinorsSaveResults($results);
    }

    return redirect('/scan-minors-secret')->with('success', "Объявление #{$postId} удалено.");
});

Route::post('/scan-minors-secret/dismiss', function (Illuminate\Http\Request $request) {
    $postId = (int) $request->input('post_id');

    $results = scanMinorsResults();
    $results = array_filter($results, fn ($r) => (int) $r['id'] !== $postId);
    scanMinorsSaveResults($results);

    return redirect('/scan-minors-secret')->with('success', "Объявление #{$postId} убрано из списка (не удалено).");
});

Route::post('/scan-minors-secret/reset', function (Illuminate\Http\Request $request) {
    scanMinorsSaveState(['cursor' => null, 'done' => 0]);

    if ($request->input('clear_results')) {
        scanMinorsSaveResults([]);
    }

    return redirect('/scan-minors-secret')->with('success', 'Позиция сброшена — сканирование начнётся заново с самых новых.');
});

// Временно: тест Claude API
Route::get('/test-ai-secret', function () {
    $apiKey = config('services.anthropic.api_key');
    if (empty($apiKey)) {
        return 'API key not set. Check ANTHROPIC_API_KEY in .env';
    }

    $masked = substr($apiKey, 0, 10).'...'.substr($apiKey, -4);
    $keyLen = strlen($apiKey);

    $proxy = config('services.anthropic.proxy');
    $proxyInfo = $proxy ? 'set' : 'NOT set';

    $http = Illuminate\Support\Facades\Http::withHeaders([
        'x-api-key' => $apiKey,
        'anthropic-version' => '2023-06-01',
        'content-type' => 'application/json',
    ])->timeout(15);

    if (! empty($proxy)) {
        $http = $http->withOptions(['proxy' => $proxy]);
    }

    $r1 = $http->post('https://api.anthropic.com/v1/messages', [
        'model' => 'claude-haiku-4-5-20251001',
        'max_tokens' => 10,
        'messages' => [['role' => 'user', 'content' => 'Say hi']],
    ]);

    return '<pre>'
        ."Key: {$masked} (length: {$keyLen})\n"
        ."Proxy: {$proxyInfo}\n\n"
        ."Status: {$r1->status()}\n"
        .'Body: '.htmlspecialchars($r1->body())."\n"
        .'</pre>';
});

// Временно: последние ошибки из лога
Route::get('/debug-log-secret', function () {
    $logFile = storage_path('logs/laravel.log');
    if (! file_exists($logFile)) {
        return 'No log file';
    }
    $lines = file($logFile);
    $last = array_slice($lines, -80);

    return '<pre>'.htmlspecialchars(implode('', $last)).'</pre>';
});

// Telegram бот: показать текущий прокси из .env (пароль замаскирован) — какой IP/порт продлевать
Route::get('/debug-proxy-secret', function () {
    $proxy = (string) config('services.telegram.proxy');

    if ($proxy === '') {
        return '<pre>TELEGRAM_PROXY не задан в .env</pre>';
    }

    $scheme = parse_url($proxy, PHP_URL_SCHEME);
    $host = parse_url($proxy, PHP_URL_HOST);
    $port = parse_url($proxy, PHP_URL_PORT);
    $user = parse_url($proxy, PHP_URL_USER);

    // Быстрая проверка доступности прокси через getMe бота
    $token = config('services.telegram.bot_token_interactive');
    $client = \Illuminate\Support\Facades\Http::asForm()->connectTimeout(5)->timeout(10)
        ->withOptions(['proxy' => $proxy]);

    $reach = '';
    try {
        $me = $client->get("https://api.telegram.org/bot{$token}/getMe")->json();
        $reach = 'getMe: '.json_encode($me, JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        $reach = 'ОШИБКА (прокси недоступен): '.$e->getMessage();
    }

    return '<pre>'.htmlspecialchars(
        "scheme: {$scheme}\n".
        "host:   {$host}\n".
        "port:   {$port}\n".
        "user:   {$user}\n".
        "pass:   (скрыт)\n\n".
        $reach
    ).'</pre>';
});

// Telegram бот: перебрать схемы прокси и найти рабочую.
// Использование: /debug-proxy-test-secret?p=HOST:PORT:USER:PASS
// (если ?p не задан — берёт текущий TELEGRAM_PROXY из .env как есть)
Route::get('/debug-proxy-test-secret', function (Illuminate\Http\Request $request) {
    $token = config('services.telegram.bot_token_interactive');

    $raw = trim((string) $request->query('p', ''));

    // Собираем список прокси-URL для проверки
    $candidates = [];
    if ($raw !== '') {
        $parts = explode(':', $raw);
        if (count($parts) === 4) {
            [$host, $port, $user, $pass] = $parts;
            $auth = rawurlencode($user).':'.rawurlencode($pass).'@';
            foreach (['http', 'socks5', 'socks5h'] as $scheme) {
                $candidates[$scheme] = "{$scheme}://{$auth}{$host}:{$port}";
            }
        } elseif (count($parts) === 2) {
            [$host, $port] = $parts;
            foreach (['http', 'socks5', 'socks5h'] as $scheme) {
                $candidates[$scheme] = "{$scheme}://{$host}:{$port}";
            }
        } else {
            return '<pre>Неверный формат ?p. Нужно HOST:PORT:USER:PASS</pre>';
        }
    } else {
        $candidates['(текущий .env)'] = (string) config('services.telegram.proxy');
    }

    $out = '';
    foreach ($candidates as $label => $proxyUrl) {
        $client = \Illuminate\Support\Facades\Http::asForm()
            ->connectTimeout(6)
            ->timeout(12)
            ->withOptions(['proxy' => $proxyUrl]);

        $masked = preg_replace('#://[^@]+@#', '://***:***@', $proxyUrl);
        try {
            $resp = $client->get("https://api.telegram.org/bot{$token}/getMe");
            $json = $resp->json();
            $ok = ($json['ok'] ?? false) ? '✅ РАБОТАЕТ' : '⚠️ ответ есть, но ok=false';
            $out .= "[{$label}] {$masked}\n   {$ok} | HTTP {$resp->status()} | ".
                json_encode($json, JSON_UNESCAPED_UNICODE)."\n\n";
        } catch (\Throwable $e) {
            $out .= "[{$label}] {$masked}\n   ❌ ".$e->getMessage()."\n\n";
        }
    }

    return '<pre>'.htmlspecialchars($out).'</pre>';
});

// Админ-бот: диагностика токена через рабочий прокси (getMe + getWebhookInfo).
// Видно: задан ли токен, валиден ли, не висит ли webhook (тогда getUpdates даёт 409).
Route::get('/debug-admin-info-secret', function () {
    $token = (string) config('services.telegram.bot_token_admin');
    $proxy = config('services.telegram.proxy');

    if ($token === '') {
        return '<pre>TELEGRAM_BOT_TOKEN_ADMIN не задан в .env</pre>';
    }

    $client = \Illuminate\Support\Facades\Http::asForm()->connectTimeout(6)->timeout(12);
    if ($proxy) {
        $client = $client->withOptions(['proxy' => $proxy]);
    }

    $tokenMasked = substr($token, 0, 12).'...'.substr($token, -4);

    try {
        $me = $client->get("https://api.telegram.org/bot{$token}/getMe")->json();
        $info = $client->get("https://api.telegram.org/bot{$token}/getWebhookInfo")->json();
    } catch (\Throwable $e) {
        return '<pre>ERROR: '.htmlspecialchars($e->getMessage()).'</pre>';
    }

    $adminIds = (string) config('services.telegram.admin_ids');

    return '<pre>'.htmlspecialchars(
        "token: {$tokenMasked}\n".
        "admin_ids: {$adminIds}\n\n".
        "getMe:\n".json_encode($me, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\n".
        "getWebhookInfo:\n".json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ).'</pre>';
});

// Telegram бот: getWebhookInfo через прокси — диагностика webhook со стороны Telegram
Route::get('/debug-tg-info-secret', function () {
    $token = config('services.telegram.bot_token_interactive');
    $proxy = config('services.telegram.proxy');

    $client = \Illuminate\Support\Facades\Http::asForm()
        ->connectTimeout(5)
        ->timeout(10);

    if ($proxy) {
        $client = $client->withOptions(['proxy' => $proxy]);
    }

    try {
        $info = $client->get("https://api.telegram.org/bot{$token}/getWebhookInfo")->json();
        $me = $client->get("https://api.telegram.org/bot{$token}/getMe")->json();
    } catch (\Throwable $e) {
        return '<pre>ERROR: '.htmlspecialchars($e->getMessage()).
            "\nproxy_set: ".($proxy ? 'yes' : 'no').'</pre>';
    }

    return '<pre>proxy_set: '.($proxy ? 'yes ('.parse_url($proxy, PHP_URL_HOST).':'.parse_url($proxy, PHP_URL_PORT).')' : 'NO').
        "\n\ngetMe:\n".htmlspecialchars(json_encode($me, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).
        "\n\ngetWebhookInfo:\n".htmlspecialchars(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).
        '</pre>';
});

// Telegram бот: логи
Route::get('/debug-telegram-secret', function () {
    // daily-канал создаёт файлы вида telegram-2026-02-24.log
    $pattern = storage_path('logs/telegram-*.log');
    $files = glob($pattern);
    if (empty($files)) {
        return 'No telegram log files';
    }
    // Берём последний файл (самый свежий)
    $logFile = end($files);
    $lines = file($logFile);
    $last = array_slice($lines, -100);

    return '<pre>'.htmlspecialchars(implode('', $last)).'</pre>';
});
