<?php

namespace App\Http\Controllers;

use App\Services\AiModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PostController extends Controller
{
    // Показать главную страницу со списком объявлений
    public function index(Request $request)
    {
        // Главная — обычный сайт знакомств: сетка анкет из таблицы profiles.
        $selectedCity = $request->get('city', 'all');
        $selectedWho = $request->get('who', 'all');

        $query = DB::table('profiles')
            ->join('users', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('city', 'profiles.city_id', '=', 'city.id')
            ->select(
                'profiles.id',
                'profiles.user_id',
                'profiles.name',
                'profiles.birthdate',
                'profiles.photo',
                'profiles.city_id',
                'users.sex',
                'city.title as city_name'
            );

        // Фильтр «Кого ищу» (1=мужчину, 2=девушку) — по полу анкеты
        if (in_array($selectedWho, ['1', '2'], true)) {
            $query->where('users.sex', $selectedWho);
        }

        // Фильтр по городу
        if ($selectedCity !== 'all' && ! empty($selectedCity)) {
            $query->where('profiles.city_id', $selectedCity);
        }

        $profiles = $query->orderByDesc('profiles.id')
            ->paginate(10)
            ->appends($request->query());

        return view('home', ['profiles' => $profiles]);
    }

    // Страница анкеты (профиль для сайта знакомств)
    public function showProfile($id)
    {
        $profile = DB::table('profiles')
            ->join('users', 'profiles.user_id', '=', 'users.id')
            ->leftJoin('city', 'profiles.city_id', '=', 'city.id')
            ->where('profiles.id', $id)
            ->select('profiles.*', 'users.sex', 'city.title as city_name')
            ->first();

        if (! $profile) {
            abort(404);
        }

        $isRegistered = (bool) session('user_id');
        $isOwner = session('user_id') && (int) session('user_id') === (int) $profile->user_id;
        $viewerHasProfile = session('user_id')
            ? DB::table('profiles')->where('user_id', session('user_id'))->exists()
            : false;

        return view('profile-show', [
            'profile' => $profile,
            'isRegistered' => $isRegistered,
            'isOwner' => $isOwner,
            'viewerHasProfile' => $viewerHasProfile,
        ]);
    }

    // Показать детальную страницу объявления
    public function show($id)
    {
        // Получаем пост по id
        $post = DB::table('post')
            ->where('id', $id)
            ->where('del', 0)
            ->first();

        if (! $post) {
            abort(404);
        }

        // Увеличиваем счётчик просмотров на 1
        DB::table('post')
            ->where('id', $post->id)
            ->increment('view');

        // Получаем все фото объявления
        $photos = DB::table('gallery')
            ->where('id_post', $post->id)
            ->get();

        // Получаем автора объявления
        $postUser = DB::table('users')
            ->where('email', $post->email)
            ->first();

        // Получаем название города
        $cityRow = DB::table('city')->where('id', $post->city)->first();
        $post->city_name = $cityRow ? $cityRow->title : $post->city;

        // Проверяем авторизацию
        $currentUser = null;
        if (session('user_id')) {
            $currentUser = DB::table('users')->where('id', session('user_id'))->first();
        }

        return view('posts.detail', [
            'post' => $post,
            'photos' => $photos,
            'postUser' => $postUser,
            'currentUser' => $currentUser,
        ]);
    }

    // Показать форму добавления объявления
    public function create()
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Для добавления объявления необходимо войти');
        }

        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Мужчина без статуса проверенного пользователя — не может добавлять
        if ($user->sex == 1 && $user->prov != 1) {
            return view('posts.need-status', compact('user'));
        }

        // Лимит: 1 объявление в сутки
        $lastPost = DB::table('post')
            ->where('email', $user->email)
            ->where('del', 0)
            ->where('date', '>=', now()->subDay())
            ->orderByDesc('date')
            ->first(['date']);

        if ($lastPost) {
            $nextAt = \Carbon\Carbon::parse($lastPost->date)->addDay();
            $cities = DB::table('city')->orderBy('id')->get();

            return view('posts.create', compact('cities', 'user', 'nextAt'));
        }

        // Получаем список городов
        $cities = DB::table('city')->orderBy('id')->get();

        return view('posts.create', compact('cities', 'user'));
    }

    // Сохранить объявление
    public function store(Request $request)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Мужчина без статуса — блокируем
        if ($user->sex == 1 && $user->prov != 1) {
            return redirect()->route('post.create');
        }

        // Лимит: 1 объявление в сутки
        $lastPost = DB::table('post')
            ->where('email', $user->email)
            ->where('del', 0)
            ->where('date', '>=', now()->subDay())
            ->first();

        if ($lastPost) {
            return redirect()->route('post.create');
        }

        $request->validate([
            'title' => 'required|max:255',
            'fio' => 'required|min:2',
            'phone' => 'nullable',
            'city' => 'required',
            'discription' => 'required',
        ], [
            'title.required' => 'Заголовок обязателен',
            'fio.required' => 'ФИО обязательно',
            'fio.min' => 'ФИО должно быть минимум 2 символа',
            'city.required' => 'Город обязателен',
            'discription.required' => 'Описание обязательно',
        ]);

        // Проверка по device_key — если с этого устройства был заблокированный аккаунт
        $deviceKey = $request->cookie('_dk');
        if ($deviceKey) {
            $deviceBlocked = DB::table('users')
                ->where('device_key', $deviceKey)
                ->where('password', '1')
                ->where('id', '!=', $user->id)
                ->exists();
            if ($deviceBlocked) {
                DB::table('users')->where('id', $user->id)->update(['password' => '1']);
                \Illuminate\Support\Facades\Log::channel('telegram')->info('DeviceBlock: заблокирован по device_key при публикации', [
                    'email' => $user->email,
                    'device_key' => $deviceKey,
                ]);
                session()->flush();
                return redirect('/');
            }
        }

        // Проверка чёрного списка Telegram
        if (\App\Services\TelegramBlacklistService::checkAndBlock(
            $user->email,
            $request->title,
            $request->fio,
            $request->discription ?? '',
            $request->telegram ?? ''
        )) {
            // Тихо делаем вид что всё ок
            session()->flush();
            return redirect('/');
        }

        // AI-привратник: проверяем объявление по правилам перед публикацией.
        // Fail-open — при сбое API объявление пропускается (см. AiModerationService).
        $check = AiModerationService::checkSubmission($request->title, $request->discription ?? '');

        // Логируем КАЖДУЮ попытку добавления (и пропущенные, и заблокированные)
        $this->logAddAttempt($request, $user, $check);

        if (! $check['allowed']) {
            return back()
                ->withErrors(['ai' => $check['reason']])
                ->withInput();
        }

        // Сохраняем объявление
        $postId = DB::table('post')->insertGetId([
            'title' => $request->title,
            'email' => $user->email,
            'email_2' => $user->email,
            'price' => '0',
            'country' => '1',
            'phone' => $request->phone ?? '',
            'whats' => $request->whats ?? '',
            'telegram' => $request->telegram ?? '',
            'fio' => $request->fio,
            'sex' => $user->sex,
            'who' => $user->sex,
            'city' => $request->city,
            'discription' => $request->discription,
            'photo_view' => $request->has('photo_view') ? 1 : 0,
            'date' => now(),
            'ip' => $request->ip(),
        ]);

        // AI-модерация временно отключена — модерируем вручную через /moderation-secret
        // $ai = AiModerationService::moderate($request->title, $request->discription);

        // Обрабатываем фото если есть
        if ($request->has('photos') && is_array($request->photos)) {
            $this->processPhotos($request->photos, $postId);
        }

        return view('posts.success', ['postId' => $postId]);
    }

    // Лог попытки добавления объявления (для отлова и обучения правил привратника).
    // Пишем в storage/app/add_attempts.jsonl по одной JSON-строке на попытку.
    private function logAddAttempt(Request $request, $user, array $check): void
    {
        try {
            $entry = json_encode([
                'at' => now()->format('Y-m-d H:i:s'),
                'email' => $user->email ?? '',
                'title' => (string) ($request->title ?? ''),
                'fio' => (string) ($request->fio ?? ''),
                'city' => (string) ($request->city ?? ''),
                'description' => (string) ($request->discription ?? ''),
                'allowed' => (bool) ($check['allowed'] ?? true),
                'reason' => (string) ($check['reason'] ?? ''),
                'ip' => $request->ip(),
            ], JSON_UNESCAPED_UNICODE);

            file_put_contents(
                storage_path('app/add_attempts.jsonl'),
                $entry."\n",
                FILE_APPEND | LOCK_EX
            );
        } catch (\Throwable $e) {
            // Лог не должен ломать публикацию
        }
    }

    // Обработка и сохранение фото
    private function processPhotos($photos, $postId)
    {
        $manager = new ImageManager(new Driver);

        // Создаем папки если их нет
        $uploadsPath = public_path('uploads/posts/'.$postId);
        $thumbsPath = public_path('uploads/posts/'.$postId.'/thumbs');

        if (! file_exists($uploadsPath)) {
            mkdir($uploadsPath, 0755, true);
        }
        if (! file_exists($thumbsPath)) {
            mkdir($thumbsPath, 0755, true);
        }

        foreach ($photos as $index => $photoData) {
            if (empty($photoData)) {
                continue;
            }

            try {
                // Декодируем base64
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));

                // Временный файл
                $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
                file_put_contents($tempFile, $imageData);

                // Генерируем уникальное имя файла
                $filename = time().'_'.$index.'.webp';

                // Сохраняем оригинал в WebP (качество 85%)
                $imageOriginal = $manager->read($tempFile);
                $originalPath = $uploadsPath.'/'.$filename;
                $imageOriginal->toWebp(85)->save($originalPath);

                // Создаем миниатюру 193x193 в WebP (качество 80%)
                $imageThumb = $manager->read($tempFile);
                $imageThumb->cover(193, 193);
                $thumbPath = $thumbsPath.'/'.$filename;
                $imageThumb->toWebp(80)->save($thumbPath);

                // Удаляем временный файл
                unlink($tempFile);

                // Сохраняем пути в БД
                DB::table('gallery')->insert([
                    'id_post' => $postId,
                    'original_webp' => 'uploads/posts/'.$postId.'/'.$filename,
                    'thumb_webp' => 'uploads/posts/'.$postId.'/thumbs/'.$filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } catch (\Exception $e) {
                \Log::error('Photo processing error: '.$e->getMessage());
            }
        }
    }

    // Генерация уникального slug
    private function generateUniqueSlug($title)
    {
        // Транслитерация и очистка
        $slug = $this->transliterate($title);
        $slug = Str::slug($slug); // Laravel helper для создания slug

        // Проверяем уникальность
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table('post')->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    // Транслитерация кириллицы в латиницу
    private function transliterate($text)
    {
        $transliteration = [
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ];

        return strtr($text, $transliteration);
    }
}
