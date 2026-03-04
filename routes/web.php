<?php

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
    Route::get('/profile/pricing', [ProfileController::class, 'pricing'])->name('profile.pricing');

    // Управление объявлениями
    Route::get('/profile/post/edit/{id}', [ProfileController::class, 'editPost'])->name('profile.post.edit');
    Route::post('/profile/post/update/{id}', [ProfileController::class, 'updatePost'])->name('profile.post.update');
    Route::post('/profile/post/delete/{id}', [ProfileController::class, 'deletePost'])->name('profile.post.delete');
    Route::post('/profile/photo/delete/{id}', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password/update', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
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

    return 'Обновлено: ' . count($result) . '<br>' . implode('<br>', $result);
});

// Telegram Bot
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook']);
Route::get('/telegram/set-webhook', [TelegramBotController::class, 'setWebhook']);

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
        if (!empty($post->title_ai)) {
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

    if (!$skip) {
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
        file_put_contents($rulesFile, $rule . PHP_EOL, FILE_APPEND);
    }

    DB::table('post')->where('id', $postId)->update(['check' => 2]);

    return redirect('/moderation-secret')->with('success', 'Правило добавлено, объявление пропущено');
});

// Временно: тест Claude API
Route::get('/test-ai-secret', function () {
    $apiKey = config('services.anthropic.api_key');
    if (empty($apiKey)) {
        return 'API key not set. Check ANTHROPIC_API_KEY in .env';
    }

    $masked = substr($apiKey, 0, 10) . '...' . substr($apiKey, -4);
    $keyLen = strlen($apiKey);

    $proxy = config('services.anthropic.proxy');
    $proxyInfo = $proxy ? 'set' : 'NOT set';

    $http = Illuminate\Support\Facades\Http::withHeaders([
        'x-api-key' => $apiKey,
        'anthropic-version' => '2023-06-01',
        'content-type' => 'application/json',
    ])->timeout(15);

    if (!empty($proxy)) {
        $http = $http->withOptions(['proxy' => $proxy]);
    }

    $r1 = $http->post('https://api.anthropic.com/v1/messages', [
        'model' => 'claude-haiku-4-5-20251001',
        'max_tokens' => 10,
        'messages' => [['role' => 'user', 'content' => 'Say hi']],
    ]);

    return '<pre>'
        . "Key: {$masked} (length: {$keyLen})\n"
        . "Proxy: {$proxyInfo}\n\n"
        . "Status: {$r1->status()}\n"
        . "Body: " . htmlspecialchars($r1->body()) . "\n"
        . '</pre>';
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
