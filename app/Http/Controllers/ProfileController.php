<?php

namespace App\Http\Controllers;

use App\Services\AiModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProfileController extends Controller
{
    // Личный кабинет (главная страница профиля)
    public function index()
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();
        $profile = DB::table('profiles')->where('user_id', $user->id)->first();

        return view('profile.index', [
            'user' => $user,
            'hasProfile' => (bool) $profile,
            'profileId' => $profile->id ?? null,
            'activeSection' => 'cabinet',
            'sectionTitle' => 'Личный кабинет',
        ]);
    }

    // Старый путь «Мои объявления» — теперь ведёт в кабинет
    public function myPosts()
    {
        return redirect()->route('profile.index');
    }

    // Настройки профиля
    public function settings()
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.settings', [
            'user' => $user,
            'activeSection' => 'settings',
            'sectionTitle' => 'Настройки профиля',
        ]);
    }

    // Моя анкета (профиль для сайта знакомств)
    public function anketa()
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();
        $profile = DB::table('profiles')->where('user_id', $user->id)->first();
        $cities = DB::table('city')->orderBy('id')->get();

        return view('profile.anketa', [
            'user' => $user,
            'profile' => $profile,
            'cities' => $cities,
            'activeSection' => 'anketa',
            'sectionTitle' => 'Моя анкета',
        ]);
    }

    // Сохранение анкеты (upsert по user_id)
    public function updateAnketa(Request $request)
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:30', 'regex:/^[\p{L}\s\-]+$/u'],
            'birthdate' => 'required|date|before:'.now()->subYears(18)->format('Y-m-d'),
            'city_id' => 'required|integer',
            'about' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:20480',
            'goal' => 'nullable|in:'.implode(',', array_keys(config('profile_options.goal'))),
            'financial' => 'nullable|in:'.implode(',', array_keys(config('profile_options.financial'))),
            'body_type' => 'nullable|in:'.implode(',', array_keys(config('profile_options.body_type'))),
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:300',
        ], [
            'name.required' => 'Укажите имя',
            'name.min' => 'Имя слишком короткое',
            'name.max' => 'Имя слишком длинное (макс. 30 символов)',
            'name.regex' => 'В поле «Имя» можно использовать только буквы (без цифр, ссылок и символов)',
            'birthdate.required' => 'Укажите дату рождения',
            'birthdate.before' => 'Регистрация только с 18 лет',
            'city_id.required' => 'Выберите город',
            'photo.image' => 'Файл должен быть изображением',
            'photo.max' => 'Фото слишком большое (макс. 20 МБ)',
        ]);

        // AI-привратник: проверяем имя и «о себе» по тем же правилам, что и объявления
        $check = AiModerationService::checkSubmission($request->name, $request->about ?? '');
        if (! $check['allowed']) {
            return back()->withErrors(['ai' => $check['reason']])->withInput();
        }

        $existing = DB::table('profiles')->where('user_id', $user->id)->first();
        $photoPath = $existing->photo ?? null;

        if ($request->hasFile('photo')) {
            $photoPath = $this->storeProfilePhoto($request->file('photo'), $user->id);
        }

        $data = [
            'name' => $request->name,
            'birthdate' => $request->birthdate,
            'city_id' => (int) $request->city_id,
            'about' => $request->about,
            'photo' => $photoPath,
            'goal' => $request->goal ?: null,
            'financial' => $request->financial ?: null,
            'body_type' => $request->body_type ?: null,
            'height' => $request->height ?: null,
            'weight' => $request->weight ?: null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('profiles')->where('user_id', $user->id)->update($data);
        } else {
            $data['user_id'] = $user->id;
            $data['created_at'] = now();
            DB::table('profiles')->insert($data);
        }

        return redirect()->route('profile.anketa')->with('success', 'Анкета сохранена');
    }

    // Удаление анкеты (вместе с фото)
    public function deleteAnketa()
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $userId = session('user_id');

        if (DB::table('profiles')->where('user_id', $userId)->exists()) {
            // Удаляем папку с фото анкеты
            $dir = public_path('uploads/profiles/'.$userId);
            if (is_dir($dir)) {
                foreach (glob($dir.'/*') as $file) {
                    @unlink($file);
                }
                @rmdir($dir);
            }

            DB::table('profiles')->where('user_id', $userId)->delete();
        }

        return redirect()->route('profile.index')->with('success', 'Анкета удалена');
    }

    // Загрузка фото анкеты в WebP
    private function storeProfilePhoto($file, $userId): string
    {
        $manager = new ImageManager(new Driver);
        $dir = public_path('uploads/profiles/'.$userId);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time().'.webp';
        $image = $manager->read($file->getRealPath());
        // Уменьшаем только если фото больше 8000px по любой стороне (иначе оставляем как есть)
        $image->scaleDown(8000, 8000);
        $image->toWebp(85)->save($dir.'/'.$filename);

        return 'uploads/profiles/'.$userId.'/'.$filename;
    }

    // Обновление профиля
    public function updateProfile(Request $request)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $request->validate([
            'fio' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'telegram_username' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9_]+$/',
        ], [
            'fio.max' => 'ФИО не должно превышать 255 символов',
            'phone.max' => 'Телефон не должен превышать 20 символов',
            'telegram_username.max' => 'Telegram username не должен превышать 100 символов',
            'telegram_username.regex' => 'Telegram username может содержать только буквы, цифры и подчеркивание',
        ]);

        // Убираем @ если пользователь случайно добавил
        $telegramUsername = $request->input('telegram_username');
        if ($telegramUsername) {
            $telegramUsername = ltrim($telegramUsername, '@');
        }

        DB::table('users')
            ->where('id', session('user_id'))
            ->update([
                'fio' => $request->input('fio'),
                'phone' => $request->input('phone'),
                'telegram_username' => $telegramUsername,
            ]);

        return back()->with('success', 'Профиль успешно обновлён');
    }

    // Смена пароля
    public function updatePassword(Request $request)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Введите текущий пароль',
            'new_password.required' => 'Введите новый пароль',
            'new_password.min' => 'Новый пароль должен быть минимум 6 символов',
            'new_password.confirmed' => 'Пароли не совпадают',
        ]);

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Проверяем текущий пароль
        if ($user->password !== sha1(md5($request->input('current_password')))) {
            return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
        }

        // Обновляем пароль
        DB::table('users')
            ->where('id', session('user_id'))
            ->update([
                'password' => sha1(md5($request->input('new_password'))),
            ]);

        return back()->with('success', 'Пароль успешно изменён');
    }

    // Удаление аккаунта
    public function deleteAccount()
    {
        if (! session('user_id')) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();
        DB::table('users')->where('id', $user->id)->update(['del' => 1]);
        DB::table('post')->where('email', $user->email)->update(['del' => 1]);
        session()->flush();

        return response()->json(['success' => true]);
    }

    // Расценки сайта
    public function pricing()
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'pricing',
            'sectionTitle' => 'Расценки сайта',
        ]);
    }

    // Редактирование объявления
    public function editPost($id)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем объявление
        $post = DB::table('post')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        // Проверяем что объявление существует и принадлежит пользователю
        if (! $post) {
            return redirect()->route('profile.posts')->with('error', 'Объявление не найдено');
        }

        // Получаем список городов
        $cities = DB::table('city')->orderBy('id')->get();

        // Получаем фотографии объявления
        $photos = DB::table('gallery')
            ->where('id_post', $id)
            ->orderBy('id')
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'post' => $post,
            'cities' => $cities,
            'photos' => $photos,
        ]);
    }

    // Обновление объявления
    public function updatePost(Request $request, $id)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Проверяем что объявление принадлежит пользователю
        $post = DB::table('post')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        if (! $post) {
            return redirect()->route('profile.posts')->with('error', 'Объявление не найдено');
        }

        // Обновляем объявление
        DB::table('post')
            ->where('id', $id)
            ->update([
                'title' => $request->input('title'),
                'fio' => $request->input('fio'),
                'discription' => $request->input('discription'),
                'city' => $request->input('city'),
                'phone' => $request->input('phone') ?? '',
                'telegram' => $request->input('telegram') ?? '',
                'whats' => $request->input('whats') ?? '',
            ]);

        // AI-модерация временно отключена — модерируем вручную через /moderation-secret
        // $ai = AiModerationService::moderate($request->input('title'), $request->input('discription'));

        // Обрабатываем новые фото если есть
        if ($request->has('photos') && is_array($request->photos)) {
            $this->processPhotos($request->photos, $id);
        }

        return redirect()->route('profile.posts')->with('success', 'Объявление успешно обновлено');
    }

    // Обработка и сохранение фото (копия из PostController)
    private function processPhotos($photos, $postId)
    {
        $uploadsPath = public_path('uploads/gallery/posts/'.$postId);
        $thumbsPath = public_path('uploads/gallery/posts/'.$postId.'/thumbs');

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

                // Генерируем уникальное имя файла
                $filename = time().'_'.$index.'.webp';

                // Используем GD для конвертации в WebP
                $image = imagecreatefromstring($imageData);

                if ($image !== false) {
                    // Сохраняем оригинал
                    $originalPath = $uploadsPath.'/'.$filename;
                    imagewebp($image, $originalPath, 85);

                    // Создаем миниатюру 193x193
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $thumb = imagecreatetruecolor(193, 193);

                    // Обрезка по центру
                    $size = min($width, $height);
                    $x = ($width - $size) / 2;
                    $y = ($height - $size) / 2;

                    imagecopyresampled($thumb, $image, 0, 0, $x, $y, 193, 193, $size, $size);

                    $thumbPath = $thumbsPath.'/'.$filename;
                    imagewebp($thumb, $thumbPath, 80);

                    imagedestroy($image);
                    imagedestroy($thumb);

                    // Сохраняем информацию в БД
                    DB::table('gallery')->insert([
                        'id_post' => $postId,
                        'original_webp' => 'uploads/gallery/posts/'.$postId.'/'.$filename,
                        'thumb_webp' => 'uploads/gallery/posts/'.$postId.'/thumbs/'.$filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Пропускаем фото с ошибкой
                continue;
            }
        }
    }

    // Удаление объявления
    public function deletePost($id)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Проверяем что объявление принадлежит пользователю
        $post = DB::table('post')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        if (! $post) {
            return response()->json(['error' => 'Объявление не найдено'], 404);
        }

        // Мягкое удаление - устанавливаем del = 1
        DB::table('post')
            ->where('id', $id)
            ->update(['del' => 1]);

        return response()->json(['success' => true, 'message' => 'Объявление удалено']);
    }

    // Удаление фото
    public function deletePhoto($id)
    {
        // Проверяем авторизацию
        if (! session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем фото
        $photo = DB::table('gallery')->where('id', $id)->first();

        if (! $photo) {
            return response()->json(['error' => 'Фото не найдено'], 404);
        }

        // Получаем объявление и проверяем что оно принадлежит пользователю
        $post = DB::table('post')
            ->where('id', $photo->id_post)
            ->where('email', $user->email)
            ->first();

        if (! $post) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // Удаляем физические файлы
        if (! empty($photo->original_webp) && file_exists(public_path($photo->original_webp))) {
            unlink(public_path($photo->original_webp));
        }
        if (! empty($photo->thumb_webp) && file_exists(public_path($photo->thumb_webp))) {
            unlink(public_path($photo->thumb_webp));
        }

        // Удаляем запись из БД
        DB::table('gallery')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Фото удалено']);
    }

    public function messages()
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем список уникальных собеседников
        $conversations = DB::select('
			SELECT
				CASE
					WHEN m.sender_id = ? THEN m.receiver_id
					ELSE m.sender_id
				END as interlocutor_id,
				MAX(m.created_at) as last_message_time,
				(SELECT message FROM messages
				 WHERE (sender_id = interlocutor_id AND receiver_id = ?)
					OR (sender_id = ? AND receiver_id = interlocutor_id)
				 ORDER BY created_at DESC LIMIT 1) as last_message,
				(SELECT COUNT(*) FROM messages
				 WHERE sender_id = interlocutor_id
				   AND receiver_id = ?
				   AND is_read = 0) as unread_count,
				(SELECT post_id FROM messages
				 WHERE (sender_id = interlocutor_id AND receiver_id = ?)
					OR (sender_id = ? AND receiver_id = interlocutor_id)
				 ORDER BY created_at DESC LIMIT 1) as post_id
			FROM messages m
			WHERE m.sender_id = ? OR m.receiver_id = ?
			GROUP BY interlocutor_id
			ORDER BY last_message_time DESC
		', [
            $user->id, $user->id, $user->id,
            $user->id, $user->id, $user->id,
            $user->id, $user->id,
        ]);

        // Получаем данные собеседников
        foreach ($conversations as $key => $conversation) {
            $interlocutor = DB::table('users')
                ->where('id', $conversation->interlocutor_id)
                ->first();

            if (! $interlocutor) {
                unset($conversations[$key]);
                continue;
            }

            $intProfile = DB::table('profiles')->where('user_id', $interlocutor->id)->first();
            $interlocutor->display_name = ($intProfile->name ?? null) ?: ($interlocutor->fio ?: explode('@', $interlocutor->email ?? '')[0]);
            $conversation->interlocutor = $interlocutor;

            if ($conversation->post_id) {
                $conversation->post = DB::table('post')
                    ->where('id', $conversation->post_id)
                    ->first();
            }
        }

        return view('profile.messages', [
            'user' => $user,
            'conversations' => $conversations,
        ]);
    }

    public function messagesChat($interlocutorId)
    {
        if (! session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Нет своей анкеты — нельзя писать сообщения
        if (! DB::table('profiles')->where('user_id', $user->id)->exists()) {
            return redirect()->route('profile.anketa')->with('error', 'Создайте анкету, чтобы писать сообщения');
        }

        $interlocutor = DB::table('users')->where('id', $interlocutorId)->first();

        if (! $interlocutor) {
            return redirect()->route('profile.messages')->with('error', 'Пользователь не найден');
        }

        // Имя собеседника берём из его анкеты
        $intProfile = DB::table('profiles')->where('user_id', $interlocutor->id)->first();
        $interlocutor->display_name = ($intProfile->name ?? null) ?: ($interlocutor->fio ?: explode('@', $interlocutor->email ?? '')[0]);

        $messages = DB::table('messages')
            ->where(function ($query) use ($user, $interlocutorId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $interlocutorId);
            })
            ->orWhere(function ($query) use ($user, $interlocutorId) {
                $query->where('sender_id', $interlocutorId)
                    ->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        DB::table('messages')
            ->where('sender_id', $interlocutorId)
            ->where('receiver_id', $user->id)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        $post = null;
        if ($messages->isNotEmpty() && $messages->first()->post_id) {
            $post = DB::table('post')
                ->where('id', $messages->first()->post_id)
                ->first();
        }

        return view('profile.chat', [
            'user' => $user,
            'interlocutor' => $interlocutor,
            'messages' => $messages,
            'post' => $post,
        ]);
    }

    public function sendMessage(Request $request)
    {
        if (! session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        // Нет своей анкеты — нельзя писать сообщения
        if (! DB::table('profiles')->where('user_id', session('user_id'))->exists()) {
            return response()->json(['error' => 'Создайте анкету, чтобы писать сообщения'], 403);
        }

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
            'post_id' => 'nullable|exists:post,id',
        ]);

        $user = DB::table('users')->where('id', session('user_id'))->first();

        if ($user->id == $request->receiver_id) {
            return response()->json(['error' => 'Нельзя отправить сообщение самому себе'], 400);
        }

        $messageId = DB::table('messages')->insertGetId([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'post_id' => $request->post_id,
            'message' => $request->message,
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $message = DB::table('messages')->where('id', $messageId)->first();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getNewMessages(Request $request, $interlocutorId)
    {
        if (! session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        $lastMessageId = $request->input('last_message_id', 0);

        $newMessages = DB::table('messages')
            ->where('sender_id', $interlocutorId)
            ->where('receiver_id', $user->id)
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($newMessages->isNotEmpty()) {
            DB::table('messages')
                ->where('sender_id', $interlocutorId)
                ->where('receiver_id', $user->id)
                ->where('id', '>', $lastMessageId)
                ->update([
                    'is_read' => 1,
                    'read_at' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'messages' => $newMessages,
        ]);
    }

    public function getUnreadCount()
    {
        if (! session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        $unreadCount = DB::table('messages')
            ->where('receiver_id', $user->id)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }
}
