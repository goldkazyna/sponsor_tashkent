<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiModerationService
{
    /**
     * Обработка текста объявления через Claude API.
     *
     * @return array{title_ai: string, discription_ai: string}
     */
    public static function moderate(string $title, string $description): array
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            Log::warning('AiModeration: ANTHROPIC_API_KEY not configured');

            return ['title_ai' => '', 'discription_ai' => ''];
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

                return ['title_ai' => '', 'discription_ai' => ''];
            }

            $content = $response->json('content.0.text', '');

            return self::parseResponse($content);
        } catch (\Exception $e) {
            Log::error('AiModeration: Exception', ['message' => $e->getMessage()]);

            return ['title_ai' => '', 'discription_ai' => ''];
        }
    }

    private static function buildPrompt(string $title, string $description): string
    {
        return <<<PROMPT
Ты — модератор сайта знакомств. Тебе дан заголовок и описание объявления.

Твоя задача:
1. Исправить грамматические и орфографические ошибки
2. Убрать лишние символы, мусор, повторяющиеся знаки препинания
3. Привести текст в аккуратный, читабельный вид
4. Сохранить исходный смысл и стиль автора
5. Не добавлять новую информацию от себя
6. Если текст уже хороший — верни его как есть

Ответ дай СТРОГО в формате:
TITLE: <исправленный заголовок>
DESCRIPTION: <исправленное описание>

Заголовок: {$title}
Описание: {$description}
PROMPT;
    }

    private static function parseResponse(string $content): array
    {
        $titleAi = '';
        $descriptionAi = '';

        if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/s', $content, $m)) {
            $titleAi = trim($m[1]);
        }

        if (preg_match('/DESCRIPTION:\s*(.+)/s', $content, $m)) {
            $descriptionAi = trim($m[1]);
        }

        // Ограничиваем заголовок 255 символами
        if (mb_strlen($titleAi) > 255) {
            $titleAi = mb_substr($titleAi, 0, 255);
        }

        return [
            'title_ai' => $titleAi,
            'discription_ai' => $descriptionAi,
        ];
    }
}
