<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiModerationService
{
    /**
     * Обработка текста объявления через Claude API.
     *
     * @return array{title_ai: string, discription_ai: string, telegram_extracted: string}
     */
    public static function moderate(string $title, string $description): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            Log::warning('AiModeration: ANTHROPIC_API_KEY not configured');

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => ''];
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

                return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => ''];
            }

            $content = $response->json('content.0.text', '');

            return self::parseResponse($content);
        } catch (\Exception $e) {
            Log::error('AiModeration: Exception', ['message' => $e->getMessage()]);

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => ''];
        }
    }

    private static function buildPrompt(string $title, string $description): string
    {
        $extraRules = '';
        $rulesFile = storage_path('app/ai_moderation_rules.txt');
        if (file_exists($rulesFile)) {
            $lines = array_filter(array_map('trim', file($rulesFile)));
            if (!empty($lines)) {
                $i = 3;
                foreach ($lines as $line) {
                    $extraRules .= "\n{$i}. {$line}";
                    $i++;
                }
            }
        }

        return <<<PROMPT
Ты — модератор сайта знакомств. Тебе дан заголовок и описание объявления.

Верни текст КАК ЕСТЬ, один в один. Не исправляй ошибки, не меняй стиль, не удаляй и не добавляй слова.

ПРАВИЛА:
1. Если в заголовке или описании есть Telegram-ник — убери его из текста, а вместо него напиши «Писать в телеграм». Сам ник верни в поле TELEGRAM. Ник может быть с @ (например @Fini2006) или без @ — просто слово на латинице (например hustlegag). Любое слово написанное латиницей которое похоже на username телеграма — считай ником.
2. Если в заголовке или описании указана цена за встречу/час/ночь (например «За встречу от 100к», «50000 за ночь», «от 30к за встречу» и подобное) — замени весь заголовок на «Ищу мужчину», а из описания тоже убери упоминание цен за встречу.{$extraRules}

Ответ дай СТРОГО в формате:
TITLE: <заголовок>
DESCRIPTION: <описание>
TELEGRAM: <извлечённый ник без @ или пусто>

Заголовок: {$title}
Описание: {$description}
PROMPT;
    }

    private static function parseResponse(string $content): array
    {
        $titleAi = '';
        $descriptionAi = '';
        $telegram = '';

        if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $titleAi = trim($m[1]);
        }

        if (preg_match('/DESCRIPTION:\s*(.+?)(?:\nTELEGRAM:|$)/s', $content, $m)) {
            $descriptionAi = trim($m[1]);
        }

        if (preg_match('/TELEGRAM:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $telegram = trim($m[1]);
            // Убираем @ если AI вернул с ним
            $telegram = ltrim($telegram, '@');
        }

        if (mb_strlen($titleAi) > 255) {
            $titleAi = mb_substr($titleAi, 0, 255);
        }

        return [
            'title_ai' => $titleAi,
            'discription_ai' => $descriptionAi,
            'telegram_extracted' => $telegram,
        ];
    }
}
