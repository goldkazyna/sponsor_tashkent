<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiModerationService
{
    /**
     * Обработка текста объявления через Claude API.
     *
     * @return array{title_ai: string, discription_ai: string, telegram_extracted: string, delete: bool}
     */
    public static function moderate(string $title, string $description): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            Log::warning('AiModeration: ANTHROPIC_API_KEY not configured');

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'phone_extracted' => '', 'whatsapp_extracted' => '', 'delete' => false, 'delete_reason' => '', 'changes' => ''];
        }

        try {
            $httpClient = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30);

            $proxy = config('services.anthropic.proxy');
            if (! empty($proxy)) {
                $httpClient = $httpClient->withOptions(['proxy' => $proxy]);
            }

            $response = $httpClient->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => self::buildPrompt($title, $description),
                    ],
                ],
            ]);

            if (! $response->successful()) {
                Log::error('AiModeration: API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'phone_extracted' => '', 'whatsapp_extracted' => '', 'delete' => false, 'delete_reason' => '', 'changes' => ''];
            }

            $content = $response->json('content.0.text', '');

            return self::parseResponse($content);
        } catch (\Exception $e) {
            Log::error('AiModeration: Exception', ['message' => $e->getMessage()]);

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'phone_extracted' => '', 'whatsapp_extracted' => '', 'delete' => false, 'delete_reason' => '', 'changes' => ''];
        }
    }

    /**
     * Извлечение Telegram-ников из полей объявлений (батч до 5 шт.).
     *
     * @param  array  $posts  — массив объектов с полями id, title, fio, discription
     * @return array<int, array{telegram: string, found_in: string}>  ключ = post id
     */
    public static function extractTelegram(array $posts): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey) || empty($posts)) {
            return [];
        }

        $postLines = '';
        foreach ($posts as $post) {
            $id = $post->id;
            $title = $post->title ?? '';
            $fio = $post->fio ?? '';
            $desc = $post->discription ?? '';
            $postLines .= "---\nID: {$id}\nTITLE: {$title}\nFIO: {$fio}\nDESCRIPTION: {$desc}\n";
        }

        $prompt = <<<PROMPT
Ты — помощник модератора. Тебе даны объявления. Для каждого найди Telegram-ник (username) если он есть в полях TITLE, FIO или DESCRIPTION.

Telegram-ник — это:
- @username или @ username (с пробелом после @) — тоже считается ником
- Просто username рядом со словами "телеграм", "тг", "tg", "telegram", "t.me"
- Ссылка t.me/username
- Замаскированные ники: буквы через точки, пробелы, нижние подчёркивания (например "t.e.l.e.g.r" или "T i 0 5 0")
- Просто username если по контексту очевидно что это ник мессенджера
- Символ @ с пробелом перед ником (например "@ lucky_elilii" = ник "lucky_elilii")

НЕ путай с именами людей, городами, обычными словами.

Для КАЖДОГО объявления выведи строку в формате:
ID: <число> | TELEGRAM: <ник без @ или пусто> | FOUND_IN: <в каком поле нашёл: title/fio/description или пусто>

Только этот формат, ничего больше.

{$postLines}
PROMPT;

        try {
            $httpClient = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30);

            $proxy = config('services.anthropic.proxy');
            if (! empty($proxy)) {
                $httpClient = $httpClient->withOptions(['proxy' => $proxy]);
            }

            $response = $httpClient->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (! $response->successful()) {
                Log::channel('telegram')->error('ExtractTelegram: API error', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $content = $response->json('content.0.text', '');
            Log::channel('telegram')->info('ExtractTelegram AI response', ['content' => $content]);

            return self::parseExtractTelegramResponse($content);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('ExtractTelegram: Exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private static function parseExtractTelegramResponse(string $content): array
    {
        $results = [];

        if (preg_match_all('/ID:\s*(\d+)\s*\|\s*TELEGRAM:\s*(.*?)\s*\|\s*FOUND_IN:\s*(.*?)(?:\n|$)/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $id = (int) $m[1];
                $telegram = trim($m[2]);
                $foundIn = trim($m[3]);

                // Убираем @ и "пусто"
                $telegram = ltrim($telegram, '@');
                if (mb_strtolower($telegram) === 'пусто' || $telegram === '' || $telegram === '-') {
                    continue;
                }

                $results[$id] = [
                    'telegram' => $telegram,
                    'found_in' => $foundIn,
                ];
            }
        }

        return $results;
    }

    private static function buildPrompt(string $title, string $description): string
    {
        $rulesBlock = '';
        $rulesFile = storage_path('app/ai_moderation_rules.txt');
        if (file_exists($rulesFile)) {
            $lines = array_filter(array_map('trim', file($rulesFile)));
            $i = 1;
            foreach ($lines as $line) {
                $rulesBlock .= "{$i}. {$line}\n";
                $i++;
            }
        }

        return <<<PROMPT
Ты — модератор сайта знакомств. Тебе дан заголовок и описание объявления.

Верни текст КАК ЕСТЬ, один в один. Не исправляй ошибки, не меняй стиль, не заменяй слова на синонимы, не добавляй слова. Единственное что ты делаешь — УБИРАЕШЬ запрещённый контент по правилам ниже. Не подменяй смысл текста.

ПРАВИЛА:
{$rulesBlock}
DELETE: yes ставь ТОЛЬКО если в правилах прямо сказано удалять (например, транс-контент, БДСМ). Если запрещённый фрагмент можно просто убрать из текста (цены, номера, ники) — убирай и оставляй объявление. НЕ удаляй объявление целиком из-за цен за встречу, кокетливых фраз и т.п.

Ответ дай СТРОГО в формате:
DELETE: <yes или no>
DELETE_REASON: <если DELETE: yes — коротко напиши причину на русском, иначе пусто>
CHANGES: <перечисли ВСЕ изменения через « - » в одну строку. Обязательно упомяни КАЖДОЕ: замены слов, удаление фраз, извлечение ников/телефонов. Например: «- заменено "финансовой основе" на "долгосрочной основе" - извлечён Telegram-ник kaz_aitu, добавлен в поле Telegram». Если ничего не менял — пусто>
TITLE: <заголовок>
DESCRIPTION: <описание>
TELEGRAM: <извлечённый ник без @ или пусто. Если нашёл ник в тексте — извлеки его в это поле и замени в DESCRIPTION на «Ваш телеграм будет показан»>
PHONE: <извлечённый номер телефона или пусто>
WHATSAPP: <номер для ватсап или пусто>

Заголовок: {$title}
Описание: {$description}
PROMPT;
    }

    private static function parseResponse(string $content): array
    {
        $titleAi = '';
        $descriptionAi = '';
        $telegram = '';
        $delete = false;
        $deleteReason = '';

        if (preg_match('/DELETE:\s*(yes|no)/i', $content, $m)) {
            $delete = strtolower(trim($m[1])) === 'yes';
        }

        if (preg_match('/DELETE_REASON:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $deleteReason = trim($m[1]);
        }

        $changes = '';
        if (preg_match('/CHANGES:\s*(.+?)(?:\n|$)/', $content, $m)) {
            $val = trim($m[1]);
            // Игнорируем если AI вернул пусто или начало другого поля
            if ($val !== '' && !preg_match('/^(TITLE:|DESCRIPTION:|TELEGRAM:|DELETE)/i', $val)) {
                $changes = $val;
            }
        }

        if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $titleAi = trim($m[1]);
        }

        if (preg_match('/DESCRIPTION:\s*(.+?)(?:\nTELEGRAM:|$)/s', $content, $m)) {
            $descriptionAi = trim($m[1]);
        }

        if (preg_match('/TELEGRAM:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $val = trim($m[1]);
            if ($val !== '' && !preg_match('/^(PHONE:|WHATSAPP:|TITLE:|DESCRIPTION:|DELETE|CHANGES:)/i', $val)) {
                $telegram = ltrim($val, '@');
            }
        }

        $phone = '';
        if (preg_match('/PHONE:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $val = trim($m[1]);
            if ($val !== '' && !preg_match('/^(WHATSAPP:|TELEGRAM:|TITLE:|DESCRIPTION:|DELETE|CHANGES:)/i', $val)) {
                $phone = $val;
            }
        }

        $whatsapp = '';
        if (preg_match('/WHATSAPP:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $val = trim($m[1]);
            if ($val !== '' && !preg_match('/^(PHONE:|TELEGRAM:|TITLE:|DESCRIPTION:|DELETE|CHANGES:)/i', $val)) {
                $whatsapp = $val;
            }
        }

        if (mb_strlen($titleAi) > 255) {
            $titleAi = mb_substr($titleAi, 0, 255);
        }

        return [
            'title_ai' => $titleAi,
            'discription_ai' => $descriptionAi,
            'telegram_extracted' => $telegram,
            'phone_extracted' => $phone,
            'whatsapp_extracted' => $whatsapp,
            'delete' => $delete,
            'delete_reason' => $deleteReason,
            'changes' => $changes,
        ];
    }
}
