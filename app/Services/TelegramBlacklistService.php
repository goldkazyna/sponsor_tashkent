<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramBlacklistService
{
    /**
     * Проверяет тексты объявления на наличие ников из чёрного списка.
     * Если найден — тихо блокирует пользователя (password = '1').
     *
     * @return bool true если заблокирован
     */
    public static function checkAndBlock(string $email, string $title, string $fio, string $discription, string $telegram): bool
    {
        $blacklist = self::getBlacklist();
        if (empty($blacklist)) {
            return false;
        }

        $foundNick = self::findInTexts($blacklist, $title, $fio, $discription, $telegram);

        if ($foundNick !== null) {
            // Тихо блокируем — ставим password = '1'
            DB::table('users')->where('email', $email)->update(['password' => '1']);

            Log::channel('telegram')->info('Blacklist: заблокирован пользователь', [
                'email' => $email,
                'found_nick' => $foundNick,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Ищет ники из чёрного списка в текстах.
     *
     * @return string|null найденный ник или null
     */
    private static function findInTexts(array $blacklist, string $title, string $fio, string $discription, string $telegram): ?string
    {
        // Проверяем поле telegram напрямую (без @)
        $telegramClean = ltrim(trim($telegram), '@');
        foreach ($blacklist as $nick) {
            if ($telegramClean !== '' && mb_strtolower($telegramClean) === mb_strtolower($nick)) {
                return $nick;
            }
        }

        // Проверяем текстовые поля — ищем ник как подстроку
        $texts = $title . ' ' . $fio . ' ' . $discription;
        $textsLower = mb_strtolower($texts);

        foreach ($blacklist as $nick) {
            $nickLower = mb_strtolower($nick);
            // Ищем @nick или просто nick
            if (mb_strpos($textsLower, '@' . $nickLower) !== false || mb_strpos($textsLower, $nickLower) !== false) {
                return $nick;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public static function getBlacklist(): array
    {
        $file = storage_path('app/telegram_blacklist.txt');
        if (! file_exists($file)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($line) {
            return ltrim(trim($line), '@');
        }, file($file))));
    }

    public static function addToBlacklist(string $nick): void
    {
        $nick = ltrim(trim($nick), '@');
        if ($nick === '') return;

        $file = storage_path('app/telegram_blacklist.txt');
        $existing = self::getBlacklist();

        if (! in_array(mb_strtolower($nick), array_map('mb_strtolower', $existing))) {
            file_put_contents($file, $nick . PHP_EOL, FILE_APPEND);
        }
    }

    public static function removeFromBlacklist(string $nick): void
    {
        $nick = ltrim(trim($nick), '@');
        $file = storage_path('app/telegram_blacklist.txt');
        if (! file_exists($file)) return;

        $lines = array_filter(array_map('trim', file($file)), function ($line) use ($nick) {
            return mb_strtolower(ltrim($line, '@')) !== mb_strtolower($nick);
        });

        file_put_contents($file, implode(PHP_EOL, $lines) . PHP_EOL);
    }
}
