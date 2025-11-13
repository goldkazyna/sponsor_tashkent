<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // Главная страница профиля (перенаправляет на мои объявления)
    public function index()
    {
        return redirect()->route('profile.posts');
    }

    // Мои объявления
    public function myPosts()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем объявления пользователя
        $posts = DB::table('posts')
            ->where('email', $user->email)
            ->where('del', 0)
            ->orderBy('date', 'desc')
            ->get();

        // Для каждого поста получаем первое фото
        foreach ($posts as $post) {
            $post->cover_img = DB::table('gallery')
                ->where('id_post', $post->id)
                ->first();
        }

        return view('profile.index', [
            'user' => $user,
            'posts' => $posts,
            'activeSection' => 'posts',
            'sectionTitle' => 'Мои объявления'
        ]);
    }

    // Сообщения
    public function messages()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'messages',
            'sectionTitle' => 'Сообщения'
        ]);
    }

    // Настройки профиля
    // Настройки профиля
public function settings()
{
    // Проверяем авторизацию
    if (!session('user_id')) {
        return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
    }

    $user = DB::table('users')->where('id', session('user_id'))->first();

    return view('profile.settings', [
        'user' => $user,
        'activeSection' => 'settings',
        'sectionTitle' => 'Настройки профиля'
    ]);
}

	// Обновление профиля
	public function updateProfile(Request $request)
	{
		// Проверяем авторизацию
		if (!session('user_id')) {
			return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
		}

		$request->validate([
			'fio' => 'nullable|string|max:255',
			'phone' => 'nullable|string|max:20',
			'telegram_username' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9_]+$/'
		], [
			'fio.max' => 'ФИО не должно превышать 255 символов',
			'phone.max' => 'Телефон не должен превышать 20 символов',
			'telegram_username.max' => 'Telegram username не должен превышать 100 символов',
			'telegram_username.regex' => 'Telegram username может содержать только буквы, цифры и подчеркивание'
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
				'telegram_username' => $telegramUsername
			]);

		return back()->with('success', 'Профиль успешно обновлён');
	}

	// Смена пароля
	public function updatePassword(Request $request)
	{
		// Проверяем авторизацию
		if (!session('user_id')) {
			return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
		}

		$request->validate([
			'current_password' => 'required',
			'new_password' => 'required|min:6|confirmed'
		], [
			'current_password.required' => 'Введите текущий пароль',
			'new_password.required' => 'Введите новый пароль',
			'new_password.min' => 'Новый пароль должен быть минимум 6 символов',
			'new_password.confirmed' => 'Пароли не совпадают'
		]);

		$user = DB::table('users')->where('id', session('user_id'))->first();

		// Проверяем текущий пароль
		if (!\Illuminate\Support\Facades\Hash::check($request->input('current_password'), $user->password)) {
			return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
		}

		// Обновляем пароль
		DB::table('users')
			->where('id', session('user_id'))
			->update([
				'password' => \Illuminate\Support\Facades\Hash::make($request->input('new_password'))
			]);

		return back()->with('success', 'Пароль успешно изменён');
	}

    // Расценки сайта
    public function pricing()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('profile.index', [
            'user' => $user,
            'activeSection' => 'pricing',
            'sectionTitle' => 'Расценки сайта'
        ]);
    }

    // Редактирование объявления
    public function editPost($id)
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем объявление
        $post = DB::table('posts')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        // Проверяем что объявление существует и принадлежит пользователю
        if (!$post) {
            return redirect()->route('profile.posts')->with('error', 'Объявление не найдено');
        }

        // Получаем список городов
        $cities = DB::table('cities')->orderBy('name')->get();

        // Получаем фотографии объявления
        $photos = DB::table('gallery')
            ->where('id_post', $id)
            ->orderBy('id')
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'post' => $post,
            'cities' => $cities,
            'photos' => $photos
        ]);
    }

    // Обновление объявления
    public function updatePost(Request $request, $id)
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Проверяем что объявление принадлежит пользователю
        $post = DB::table('posts')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        if (!$post) {
            return redirect()->route('profile.posts')->with('error', 'Объявление не найдено');
        }

        // Обновляем объявление
        DB::table('posts')
            ->where('id', $id)
            ->update([
                'title' => $request->input('title'),
                'fio' => $request->input('fio'),
                'description' => $request->input('description'),
                'city' => $request->input('city'),
                'phone' => $request->input('phone'),
                'telegram' => $request->input('telegram', ''),
                'whats' => $request->input('whats', '')
            ]);

        // Обрабатываем новые фото если есть
        if ($request->has('photos') && is_array($request->photos)) {
            $this->processPhotos($request->photos, $id);
        }

        return redirect()->route('profile.posts')->with('success', 'Объявление успешно обновлено');
    }

    // Обработка и сохранение фото (копия из PostController)
    private function processPhotos($photos, $postId)
    {
        $uploadsPath = public_path('uploads/gallery/posts/' . $postId);
        $thumbsPath = public_path('uploads/gallery/posts/' . $postId . '/thumbs');
        
        if (!file_exists($uploadsPath)) {
            mkdir($uploadsPath, 0755, true);
        }
        if (!file_exists($thumbsPath)) {
            mkdir($thumbsPath, 0755, true);
        }
        
        foreach ($photos as $index => $photoData) {
            if (empty($photoData)) continue;
            
            try {
                // Декодируем base64
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
                
                // Генерируем уникальное имя файла
                $filename = time() . '_' . $index . '.webp';
                
                // Используем GD для конвертации в WebP
                $image = imagecreatefromstring($imageData);
                
                if ($image !== false) {
                    // Сохраняем оригинал
                    $originalPath = $uploadsPath . '/' . $filename;
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
                    
                    $thumbPath = $thumbsPath . '/' . $filename;
                    imagewebp($thumb, $thumbPath, 80);
                    
                    imagedestroy($image);
                    imagedestroy($thumb);
                    
                    // Сохраняем информацию в БД
                    DB::table('gallery')->insert([
                        'id_post' => $postId,
                        'original_webp' => 'uploads/gallery/posts/' . $postId . '/' . $filename,
                        'thumb_webp' => 'uploads/gallery/posts/' . $postId . '/thumbs/' . $filename,
                        'created_at' => now(),
                        'updated_at' => now()
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
        if (!session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Проверяем что объявление принадлежит пользователю
        $post = DB::table('posts')
            ->where('id', $id)
            ->where('email', $user->email)
            ->first();

        if (!$post) {
            return response()->json(['error' => 'Объявление не найдено'], 404);
        }

        // Мягкое удаление - устанавливаем del = 1
        DB::table('posts')
            ->where('id', $id)
            ->update(['del' => 1]);

        return response()->json(['success' => true, 'message' => 'Объявление удалено']);
    }

    // Удаление фото
    public function deletePhoto($id)
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return response()->json(['error' => 'Необходимо авторизоваться'], 401);
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Получаем фото
        $photo = DB::table('gallery')->where('id', $id)->first();

        if (!$photo) {
            return response()->json(['error' => 'Фото не найдено'], 404);
        }

        // Получаем объявление и проверяем что оно принадлежит пользователю
        $post = DB::table('posts')
            ->where('id', $photo->id_post)
            ->where('email', $user->email)
            ->first();

        if (!$post) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // Удаляем физические файлы
        if (!empty($photo->original_webp) && file_exists(public_path($photo->original_webp))) {
            unlink(public_path($photo->original_webp));
        }
        if (!empty($photo->thumb_webp) && file_exists(public_path($photo->thumb_webp))) {
            unlink(public_path($photo->thumb_webp));
        }

        // Удаляем запись из БД
        DB::table('gallery')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Фото удалено']);
    }
}