<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceFingerprint
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Если cookie уже есть — не трогаем
        if ($request->cookie('_dk')) {
            return $response;
        }

        // Ставим persistent cookie на 5 лет
        $deviceKey = uniqid('dk_', true);
        $response->headers->setCookie(
            cookie('_dk', $deviceKey, 60 * 24 * 365 * 5, '/', null, false, true)
        );

        return $response;
    }
}
