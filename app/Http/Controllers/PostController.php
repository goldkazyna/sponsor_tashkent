<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PostController extends Controller
{
    // Показать главную страницу со списком объявлений
	public function index(Request $request)
	{
		// Получаем параметр города из запроса
		$selectedCity = $request->get('city', 'all');
		
		// Строим запрос постов
		$query = DB::table('posts')->where('del', 0);
		
		// Если выбран конкретный город (не "all"), добавляем фильтр
		if ($selectedCity !== 'all' && !empty($selectedCity)) {
			$query->where('city', $selectedCity);
		}
		
		// Сортировка по id DESC (новые первыми) и пагинация
		$posts = $query->orderBy('id', 'desc')->paginate(10);
		
		// Для каждого поста получаем первое фото
		foreach ($posts as $post) {
			$post->cover_img = DB::table('gallery')
				->where('id_post', $post->id)
				->first();
		}
		
		// Проверяем авторизацию
		$currentUser = null;
		if (session('user_id')) {
			$currentUser = DB::table('users')->where('id', session('user_id'))->first();
		}
		
		// Добавляем параметр города к пагинации
		$posts->appends(['city' => $selectedCity]);
		
		return view('home', [
			'posts' => $posts,
			'currentUser' => $currentUser,
			'selectedCity' => $selectedCity
		]);
	}

    // Показать детальную страницу объявления
    public function show($slug)
    {
        // Получаем пост по slug
        $post = DB::table('posts')
            ->where('slug', $slug)
            ->where('del', 0)
            ->first();
        
        if (!$post) {
            abort(404);
        }
        
        // Увеличиваем счётчик просмотров на 1
        DB::table('posts')
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
        
        // Проверяем авторизацию
        $currentUser = null;
        if (session('user_id')) {
            $currentUser = DB::table('users')->where('id', session('user_id'))->first();
        }
        
        return view('posts.detail', [
            'post' => $post,
            'photos' => $photos,
            'postUser' => $postUser,
            'currentUser' => $currentUser
        ]);
    }

    // Показать форму добавления объявления
    public function create()
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Для добавления объявления необходимо войти');
        }

        // Получаем список городов
        $cities = DB::table('cities')->orderBy('name')->get();
        
        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('posts.create', compact('cities', 'user'));
    }

    // Сохранить объявление
    public function store(Request $request)
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'title' => 'required|max:255',
            'fio' => 'required|min:2',
            'phone' => 'required',
            'city' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'Заголовок обязателен',	
            'fio.required' => 'ФИО обязательно',
            'fio.min' => 'ФИО должно быть минимум 2 символа',
            'phone.required' => 'Телефон обязателен',
            'city.required' => 'Город обязателен',
            'description.required' => 'Описание обязательно'
        ]);

        // Генерируем slug из title
        $slug = $this->generateUniqueSlug($request->title);

        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Сохраняем объявление
        $postId = DB::table('posts')->insertGetId([
            'title' => $request->title,
            'slug' => $slug,
            'email' => $user->email,
            'phone' => $request->phone,
            'whats' => $request->whats ?? '',
            'telegram' => $request->telegram ?? '',
            'fio' => $request->fio,
            'sex' => $user->sex,
            'who' => $user->sex,
            'city' => $request->city,
            'description' => $request->description,
            'photo_view' => $request->has('photo_view') ? 1 : 0,
            'date' => now(),
            'ip' => $request->ip(),
        ]);

        // Обрабатываем фото если есть
        if ($request->has('photos') && is_array($request->photos)) {
            $this->processPhotos($request->photos, $postId);
        }

        return view('posts.success', ['slug' => $slug]);
    }

    // Обработка и сохранение фото
    private function processPhotos($photos, $postId)
    {
        $manager = new ImageManager(new Driver());
        
        // Создаем папки если их нет
        $uploadsPath = public_path('uploads/posts/' . $postId);
        $thumbsPath = public_path('uploads/posts/' . $postId . '/thumbs');
        
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
                
                // Временный файл
                $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
                file_put_contents($tempFile, $imageData);
                
                // Генерируем уникальное имя файла
                $filename = time() . '_' . $index . '.webp';
                
                // Сохраняем оригинал в WebP (качество 85%)
                $imageOriginal = $manager->read($tempFile);
                $originalPath = $uploadsPath . '/' . $filename;
                $imageOriginal->toWebp(85)->save($originalPath);
                
                // Создаем миниатюру 193x193 в WebP (качество 80%)
                $imageThumb = $manager->read($tempFile);
                $imageThumb->cover(193, 193);
                $thumbPath = $thumbsPath . '/' . $filename;
                $imageThumb->toWebp(80)->save($thumbPath);
                
                // Удаляем временный файл
                unlink($tempFile);
                
                // Сохраняем пути в БД
                DB::table('gallery')->insert([
                    'id_post' => $postId,
                    'original_webp' => 'uploads/posts/' . $postId . '/' . $filename,
                    'thumb_webp' => 'uploads/posts/' . $postId . '/thumbs/' . $filename,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Photo processing error: ' . $e->getMessage());
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
        
        while (DB::table('posts')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
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
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
        ];
        
        return strtr($text, $transliteration);
    }
}