<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Сервис переведён в режим «только Telegram».
 *
 * Любой веб-запрос отдаёт страницу-заглушку с переходом в бота,
 * КРОМЕ путей, которые должны продолжать работать:
 *  - бот и админ-бот (webhook/polling)
 *  - автологин из бота
 *  - все секретные роуты (модерация, отладка и т.п.)
 *  - health-check /up
 *
 * Чтобы вернуть обычный сайт — убрать этот middleware из bootstrap/app.php.
 */
class TelegramOnly
{
    /** @var string[] Префиксы путей, которые продолжают работать. */
    private array $allowedPrefixes = [
        'telegram',
        'admin-bot',
        'auto-login',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path(); // без ведущего слеша; для '/' вернёт ''

        foreach ($this->allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return $next($request);
            }
        }

        // Любой секретный роут (содержит "secret" в пути)
        if (str_contains($path, 'secret')) {
            return $next($request);
        }

        return response()->view('telegram-only', [], 200);
    }
}
