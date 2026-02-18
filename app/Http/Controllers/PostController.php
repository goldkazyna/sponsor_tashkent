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
		// Получаем параметры фильтров
		$selectedCity = $request->get('city', 'all');
		$selectedWho = $request->get('who', 'all');

		// Строим запрос постов
		$query = DB::table('post')->where('del', 0);

		// Фильтр по городу
		if ($selectedCity !== 'all' && !empty($selectedCity)) {
			$query->where('city', $selectedCity);
		}

		// Фильтр "Кого ищу" (who: 1=спонсор ищет женщину, 2=содержанка ищет мужчину)
		if ($selectedWho !== 'all' && in_array($selectedWho, ['1', '2'])) {
			$query->where('who', $selectedWho);
		}
		
		// Сортировка по id DESC (новые первыми) и пагинация
		$posts = $query->orderBy('id', 'desc')->paginate(10);
		
		// Для каждого поста получаем первое фото и название города
		foreach ($posts as $post) {
			$post->cover_img = DB::table('gallery')
				->where('id_post', $post->id)
				->first();
			$cityRow = DB::table('city')->where('id', $post->city)->first();
			$post->city_name = $cityRow ? $cityRow->title : $post->city;
		}
		
		// Проверяем авторизацию
		$currentUser = null;
		if (session('user_id')) {
			$currentUser = DB::table('users')->where('id', session('user_id'))->first();
		}
		
		// Увеличиваем view +1 для всех объявлений на текущей странице
		$postIds = $posts->pluck('id')->toArray();
		if (!empty($postIds)) {
			DB::table('post')->whereIn('id', $postIds)->increment('view');
		}

		// Добавляем параметр города к пагинации
		$posts->appends(['city' => $selectedCity, 'who' => $selectedWho]);
		
		// ТОП объявления: 2 с наименьшим count_view (активные по date_end)
		$topPosts = DB::table('top_post')
			->where('date_end', '>=', now())
			->orderBy('count_view', 'asc')
			->limit(2)
			->get();

		$topPostsData = collect();
		if ($topPosts->count() > 0) {
			$topIds = $topPosts->pluck('id_post')->toArray();
			$topViewMap = $topPosts->pluck('count_view', 'id_post')->toArray();
			$topRecordIds = $topPosts->pluck('id', 'id_post')->toArray();

			$topPostsData = DB::table('post')
				->whereIn('id', $topIds)
				->where('del', 0)
				->get();

			foreach ($topPostsData as $tp) {
				$tp->cover_img = DB::table('gallery')->where('id_post', $tp->id)->first();
				$cityRow = DB::table('city')->where('id', $tp->city)->first();
				$tp->city_name = $cityRow ? $cityRow->title : $tp->city;
				$tp->top_views = $topViewMap[$tp->id] ?? 0;
			}

			// Обновляем count_view +1
			DB::table('top_post')
				->whereIn('id', $topPosts->pluck('id')->toArray())
				->increment('count_view');
		}

		return view('home', [
			'posts' => $posts,
			'topPosts' => $topPostsData,
			'currentUser' => $currentUser,
			'selectedCity' => $selectedCity
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
        
        if (!$post) {
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

        // Получаем данные пользователя
        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Мужчина без статуса проверенного спонсора — не может добавлять
        if ($user->sex == 1 && $user->prov != 1) {
            return view('posts.need-status', compact('user'));
        }

        // Получаем список городов
        $cities = DB::table('city')->orderBy('id')->get();

        return view('posts.create', compact('cities', 'user'));
    }

    // Сохранить объявление
    public function store(Request $request)
    {
        // Проверяем авторизацию
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        // Мужчина без статуса — блокируем
        if ($user->sex == 1 && $user->prov != 1) {
            return redirect()->route('post.create');
        }

        $request->validate([
            'title' => 'required|max:255',
            'fio' => 'required|min:2',
            'phone' => 'required',
            'city' => 'required',
            'discription' => 'required'
        ], [
            'title.required' => 'Заголовок обязателен',	
            'fio.required' => 'ФИО обязательно',
            'fio.min' => 'ФИО должно быть минимум 2 символа',
            'phone.required' => 'Телефон обязателен',
            'city.required' => 'Город обязателен',
            'discription.required' => 'Описание обязательно'
        ]);

        // Сохраняем объявление
        $postId = DB::table('post')->insertGetId([
            'title' => $request->title,
            'email' => $user->email,
            'email_2' => $user->email,
            'price' => '0',
            'country' => '1',
            'phone' => $request->phone,
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

        // Обрабатываем фото если есть
        if ($request->has('photos') && is_array($request->photos)) {
            $this->processPhotos($request->photos, $postId);
        }

        return view('posts.success', ['postId' => $postId]);
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
        
        while (DB::table('post')->where('slug', $slug)->exists()) {
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