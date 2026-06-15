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
     * Привратник при добавлении объявления: проверяет заголовок и описание по
     * правилам из storage/app/add_moderation_rules.txt.
     *
     * Fail-open: при любой неудаче (нет ключа, нет/пустой файл правил, ошибка API,
     * неразборчивый ответ) объявление пропускается.
     *
     * @return array{allowed: bool, reason: string}
     */
    public static function checkSubmission(string $title, string $description): array
    {
        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return ['allowed' => true, 'reason' => ''];
        }

        $rules = self::loadAddRules();
        if ($rules === '') {
            // Нет правил — нечего нарушать
            return ['allowed' => true, 'reason' => ''];
        }

        $prompt = <<<PROMPT
Ты — модератор-привратник сайта знакомств. Тебе дан заголовок и описание объявления. Ниже список правил. Реши, нарушает ли объявление ХОТЯ БЫ ОДНО правило.

ПРАВИЛА (нарушение любого = блокировка):
{$rules}

Ответь СТРОГО в формате (без лишнего текста):
VERDICT: <allow или block>
REASON: <если block — короткая понятная причина на русском для пользователя, почему нельзя опубликовать; если allow — пусто. Если нарушено НЕСКОЛЬКО правил — перечисли причины в одной строке REASON, разделяя их символом «|» (вертикальная черта), без нумерации>

Заголовок: {$title}
Описание: {$description}
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
                'max_tokens' => 512,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (! $response->successful()) {
                Log::channel('telegram')->error('CheckSubmission: API error', ['status' => $response->status(), 'body' => mb_substr($response->body(), 0, 500)]);

                return ['allowed' => true, 'reason' => ''];
            }

            $content = $response->json('content.0.text', '');
            $result = self::parseSubmissionVerdict($content);

            Log::channel('telegram')->info('CheckSubmission verdict', [
                'title' => $title,
                'allowed' => $result['allowed'],
                'reason' => $result['reason'],
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::channel('telegram')->error('CheckSubmission: Exception', ['message' => $e->getMessage()]);

            return ['allowed' => true, 'reason' => ''];
        }
    }

    /**
     * Разбор ответа привратника. Fail-open: всё, что не «block» по формату — allow.
     *
     * @return array{allowed: bool, reason: string}
     */
    public static function parseSubmissionVerdict(string $content): array
    {
        if (! preg_match('/VERDICT:\s*(allow|block)/i', $content, $m)) {
            return ['allowed' => true, 'reason' => ''];
        }

        if (mb_strtolower(trim($m[1])) !== 'block') {
            return ['allowed' => true, 'reason' => ''];
        }

        $reason = '';
        if (preg_match('/REASON:\s*(.+?)(?:\n|$)/su', $content, $rm)) {
            $val = trim($rm[1]);
            if ($val !== '' && ! preg_match('/^VERDICT:/i', $val)) {
                $reason = $val;
            }
        }

        if ($reason === '') {
            $reason = 'Объявление нарушает правила сайта.';
        }

        return ['allowed' => false, 'reason' => $reason];
    }

    /**
     * Загружает правила привратника из файла, по одной на строку (пустые игнорируются).
     */
    private static function loadAddRules(): string
    {
        $rulesFile = storage_path('app/add_moderation_rules.txt');
        if (! file_exists($rulesFile)) {
            return '';
        }

        $lines = array_filter(array_map('trim', file($rulesFile)));
        $block = '';
        $i = 1;
        foreach ($lines as $line) {
            $block .= "{$i}. {$line}\n";
            $i++;
        }

        return trim($block);
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

    /**
     * Поиск объявлений с упоминанием несовершеннолетних (16, 17 и младше) или девственности.
     * Ничего не удаляет — только помечает для ручного просмотра. Батч до ~25 шт.
     *
     * @param  array  $posts  — массив объектов с полями id, title, fio, discription
     * @return array<int, array{reason: string, fragment: string}>  ключ = post id (только помеченные)
     */
    public static function detectMinorsAndVirgin(array $posts): array
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
Ты — помощник модератора сайта знакомств. Тебе даны объявления. Для КАЖДОГО определи, есть ли в полях TITLE, FIO или DESCRIPTION один из двух признаков:

1. ВОЗРАСТ 16, 17 лет ИЛИ МЛАДШЕ (несовершеннолетние, до 18 лет включительно НЕ считается — 18 это норма, НЕ помечай 18 и старше).
   Учитывай замаскированные и словесные формы: "16", "17", "шестнадцать", "семнадцать", "16лет", "1 6", цифры через пробелы/точки, "мне 16", "16 годиков" и т.п.
   НЕ путай с ростом, весом, размером, ценой, количеством, номерами телефонов, годом. Только если по смыслу это ВОЗРАСТ человека и он 17 или меньше.

2. ДЕВСТВЕННОСТЬ — упоминание что человек девственница/девственник, "невинная", "без опыта в этом смысле", "первый раз", "цела" и подобное по смыслу.

Для КАЖДОГО объявления выведи РОВНО одну строку строго в формате:
ID: <число> | FLAG: <yes или no> | REASON: <age, virgin, both или пусто> | FRAGMENT: <короткая цитата из текста где нашёл, или пусто>

FLAG: yes только если уверен. Если не уверен или признаков нет — FLAG: no. Только этот формат, ничего больше.

{$postLines}
PROMPT;

        try {
            $httpClient = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(60);

            $proxy = config('services.anthropic.proxy');
            if (! empty($proxy)) {
                $httpClient = $httpClient->withOptions(['proxy' => $proxy]);
            }

            $response = $httpClient->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 2048,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (! $response->successful()) {
                Log::channel('telegram')->error('DetectMinors: API error', ['status' => $response->status(), 'body' => $response->body()]);

                return [];
            }

            $content = $response->json('content.0.text', '');

            return self::parseDetectMinorsResponse($content);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('DetectMinors: Exception', ['message' => $e->getMessage()]);

            return [];
        }
    }

    private static function parseDetectMinorsResponse(string $content): array
    {
        $results = [];

        if (preg_match_all('/ID:\s*(\d+)\s*\|\s*FLAG:\s*(\w+)\s*\|\s*REASON:\s*(.*?)\s*\|\s*FRAGMENT:\s*(.*?)(?:\n|$)/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $id = (int) $m[1];
                $flag = mb_strtolower(trim($m[2]));
                $reason = mb_strtolower(trim($m[3]));
                $fragment = trim($m[4]);

                if ($flag !== 'yes') {
                    continue;
                }

                $results[$id] = [
                    'reason' => $reason,
                    'fragment' => $fragment,
                ];
            }
        }

        return $results;
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
