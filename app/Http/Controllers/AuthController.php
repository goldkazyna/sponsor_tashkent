<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // Показать форму регистрации
    public function showRegister()
    {
        return view('auth.register');
    }

    // Регистрация пользователя
	public function register(Request $request)
	{
		$request->validate([
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6|confirmed',
			'sex' => 'required|in:1,2',
			'agree' => 'accepted',
		], [
			'email.required' => 'Email обязателен',
			'email.email' => 'Введите корректный email',
			'email.unique' => 'Этот email уже зарегистрирован',
			'password.required' => 'Пароль обязателен',
			'password.min' => 'Пароль должен быть минимум 6 символов',
			'password.confirmed' => 'Пароли не совпадают',
			'sex.required' => 'Выберите пол',
			'agree.accepted' => 'Нужно принять Пользовательское соглашение и Правила сайта',
		]);

		$cookieKey = $request->cookie('_dk');
		$deviceKey = $cookieKey ?? uniqid('dk_', true);

		// Проверяем: если с этого устройства уже был заблокированный аккаунт — тихо блокируем
		// Только если cookie реально пришла (не сгенерированная на лету)
		$blocked = false;
		if ($cookieKey) {
			$blocked = DB::table('users')
				->where('device_key', $cookieKey)
				->where('password', '1')
				->exists();
		}

		// Создаем пользователя
		DB::table('users')->insert([
			'email' => $request->email,
			'password' => $blocked ? '1' : sha1(md5($request->password)),
			'sex' => $request->sex,
			'ip' => $request->ip(),
			'date' => now(),
			'activate' => 1,
			'confirm' => 1,
			'status' => 0,
			'device_key' => $deviceKey,
			'fio' => '',
			'phone' => '',
			'activate_code' => '',
			'restore_code' => '',
			'prov' => 0,
			'telegram_id' => '',
			'telegram_username' => '',
		]);

		if ($blocked) {
			\Illuminate\Support\Facades\Log::channel('telegram')->info('DeviceBlock: новый аккаунт заблокирован по device_key', [
				'email' => $request->email,
				'device_key' => $deviceKey,
			]);
		}

		return view('auth.register-success');
	}

    // Показать форму входа
    public function showLogin()
    {
        return view('auth.login');
    }

    // Вход пользователя
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email обязателен',
            'email.email' => 'Введите корректный email',
            'password.required' => 'Пароль обязателен'
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && !empty($user->del)) {
            return back()->withErrors(['email' => 'Этот аккаунт был удалён'])->withInput();
        }

        if ($user && $user->password === sha1(md5($request->password))) {
            // Успешный вход
            session(['user_id' => $user->id]);
            session(['user_email' => $user->email]);
            session(['user_sex' => $user->sex]);
            
            return redirect('/')->with('success', 'Добро пожаловать!');
        }

        return back()->withErrors(['email' => 'Неверный email или пароль'])->withInput();
    }

    // Выход
    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Вы вышли из системы');
    }
	
	// Показать форму запроса восстановления
	public function showResetRequest()
	{
		return view('auth.passwords.email');
	}

	// Отправить ссылку для восстановления
	public function sendResetLink(Request $request)
	{
		$request->validate([
			'email' => 'required|email|exists:users,email'
		], [
			'email.required' => 'Email обязателен',
			'email.email' => 'Введите корректный email',
			'email.exists' => 'Пользователь с таким email не найден'
		]);

		// Генерируем код восстановления
		$resetCode = Str::random(60);
		
		// Сохраняем код в БД
		DB::table('users')
			->where('email', $request->email)
			->update(['restore_code' => $resetCode]);

		// Отправляем письмо
		$resetUrl = route('password.reset', ['code' => $resetCode]);
		
		Mail::raw("Для восстановления пароля перейдите по ссылке: {$resetUrl}", function($message) use ($request) {
			$message->to($request->email)
					->subject('Восстановление пароля - знакомства.KZ');
		});

		return back()->with('success', 'Ссылка для восстановления пароля отправлена на ваш email!');
	}

	// Показать форму смены пароля
	public function showResetForm($code)
	{
		$user = DB::table('users')->where('restore_code', $code)->first();
		
		if (!$user) {
			return redirect()->route('login')->withErrors(['error' => 'Неверная ссылка восстановления']);
		}
		
		return view('auth.passwords.reset', ['code' => $code]);
	}

	// Сменить пароль
	public function resetPassword(Request $request)
	{
		$request->validate([
			'code' => 'required',
			'password' => 'required|min:6|confirmed'
		], [
			'password.required' => 'Пароль обязателен',
			'password.min' => 'Пароль должен быть минимум 6 символов',
			'password.confirmed' => 'Пароли не совпадают'
		]);

		$user = DB::table('users')->where('restore_code', $request->code)->first();
		
		if (!$user) {
			return back()->withErrors(['error' => 'Неверный код восстановления']);
		}

		// Обновляем пароль и очищаем код
		DB::table('users')
			->where('id', $user->id)
			->update([
				'password' => sha1(md5($request->password)),
				'restore_code' => ''
			]);

		return redirect()->route('login')->with('success', 'Пароль успешно изменен! Войдите с новым паролем.');
	}
}