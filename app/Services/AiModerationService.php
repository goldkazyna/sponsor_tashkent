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

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'delete' => false];
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

                return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'delete' => false];
            }

            $content = $response->json('content.0.text', '');

            return self::parseResponse($content);
        } catch (\Exception $e) {
            Log::error('AiModeration: Exception', ['message' => $e->getMessage()]);

            return ['title_ai' => '', 'discription_ai' => '', 'telegram_extracted' => '', 'delete' => false];
        }
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

Верни текст КАК ЕСТЬ, один в один. Не исправляй ошибки, не меняй стиль, не удаляй и не добавляй слова.

ПРАВИЛА:
{$rulesBlock}
DELETE: yes ставь ТОЛЬКО если в правилах прямо сказано удалять (например, транс-контент). Обычные объявления о знакомствах НЕ удаляй, даже если в них есть кокетливые фразы вроде «знаю толк в обольщении», «умею удивить» и подобное — это нормально для сайта знакомств.

Ответ дай СТРОГО в формате:
DELETE: <yes или no>
DELETE_REASON: <если DELETE: yes — коротко напиши причину на русском, иначе пусто>
CHANGES: <если ты что-то изменил в тексте — коротко перечисли что и почему на русском, иначе пусто>
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
        $delete = false;
        $deleteReason = '';

        if (preg_match('/DELETE:\s*(yes|no)/i', $content, $m)) {
            $delete = strtolower(trim($m[1])) === 'yes';
        }

        if (preg_match('/DELETE_REASON:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $deleteReason = trim($m[1]);
        }

        $changes = '';
        if (preg_match('/CHANGES:\s*(.+?)(?:\nTITLE:|$)/s', $content, $m)) {
            $changes = trim($m[1]);
        }

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
            'delete' => $delete,
            'delete_reason' => $deleteReason,
            'changes' => $changes,
        ];
    }
}
