<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    // Показать форму
    public function show()
    {
        $user = null;
        
        // Если юзер авторизован - получаем его данные
        if (session('user_id')) {
            $user = DB::table('users')->where('id', session('user_id'))->first();
        }
        
        return view('contact', compact('user'));
    }
    
    // Отправить сообщение в Telegram
    public function send(Request $request)
    {
        // Валидация
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'contact_type' => 'nullable|in:whatsapp,telegram',
            'contact_value' => 'nullable|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ], [
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email',
            'message.required' => 'Сообщение обязательно для заполнения',
            'message.min' => 'Сообщение должно быть минимум 10 символов',
            'message.max' => 'Сообщение не должно превышать 2000 символов',
        ]);
        
        // Формируем сообщение для Telegram
        $telegramMessage = "🔔 <b>Новое сообщение с сайта</b>\n\n";
        
        if ($request->name) {
            $telegramMessage .= "👤 <b>Имя:</b> {$request->name}\n";
        }
        
        $telegramMessage .= "📧 <b>Email:</b> {$request->email}\n";
        
        if ($request->contact_type && $request->contact_value) {
            $contactLabel = $request->contact_type == 'whatsapp' ? '📱 WhatsApp' : '💬 Telegram';
            $telegramMessage .= "{$contactLabel}: {$request->contact_value}\n";
        }
        
        $telegramMessage .= "\n💌 <b>Сообщение:</b>\n{$request->message}";
        
        // Отправляем в Telegram
        $sent = $this->sendToTelegram($telegramMessage);
        
        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки. Попробуйте позже или свяжитесь напрямую через Telegram.'
            ], 500);
        }
    }
    
    // Показать форму поднятия в ТОП
    public function showBoostTop(Request $request)
    {
        $user = null;
        if (session('user_id')) {
            $user = DB::table('users')->where('id', session('user_id'))->first();
        }
        $postId = $request->get('post_id');
        $post = $postId ? DB::table('post')->where('id', $postId)->first() : null;
        return view('boost-top', compact('user', 'post'));
    }

    // Отправить заявку на поднятие в ТОП
    public function sendBoostTop(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'telegram' => 'required|string|max:255',
            'post_id' => 'required',
            'message' => 'nullable|string|max:2000',
        ], [
            'email.required' => 'Email обязателен',
            'email.email' => 'Введите корректный email',
            'telegram.required' => 'Telegram обязателен для связи',
            'post_id.required' => 'ID объявления обязателен',
        ]);

        $telegramMessage = "🚀 <b>Заявка на поднятие в ТОП</b>\n\n";
        $telegramMessage .= "📋 <b>ID объявления:</b> {$request->post_id}\n";

        if ($request->name) {
            $telegramMessage .= "👤 <b>Имя:</b> {$request->name}\n";
        }

        $telegramMessage .= "📧 <b>Email:</b> {$request->email}\n";
        $telegramMessage .= "💬 <b>Telegram:</b> {$request->telegram}\n";

        if ($request->message) {
            $telegramMessage .= "\n💌 <b>Комментарий:</b>\n{$request->message}";
        }

        $sent = $this->sendToTelegram($telegramMessage);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Заявка отправлена! Мы свяжемся с вами в Telegram.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки. Попробуйте позже.'
            ], 500);
        }
    }

    // Показать форму покупки статуса
    public function showVerified()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Для покупки статуса необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();
        return view('become-verified', compact('user'));
    }

    // Отправить заявку на покупку статуса
    public function sendVerified(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'telegram' => 'required|string|max:255',
            'message' => 'nullable|string|max:2000',
        ], [
            'email.required' => 'Email обязателен',
            'email.email' => 'Введите корректный email',
            'telegram.required' => 'Telegram обязателен для связи',
        ]);

        $telegramMessage = "⭐ <b>Заявка на статус проверенного спонсора</b>\n\n";

        if ($request->name) {
            $telegramMessage .= "👤 <b>Имя:</b> {$request->name}\n";
        }

        $telegramMessage .= "📧 <b>Email:</b> {$request->email}\n";
        $telegramMessage .= "💬 <b>Telegram:</b> {$request->telegram}\n";

        if ($request->message) {
            $telegramMessage .= "\n💌 <b>Комментарий:</b>\n{$request->message}";
        }

        $sent = $this->sendToTelegram($telegramMessage);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Заявка отправлена! Мы свяжемся с вами в Telegram в ближайшее время.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка отправки. Попробуйте позже.'
            ], 500);
        }
    }

    // Отправка в Telegram Bot API
    private function sendToTelegram($message)
	{
		$botToken = env('TELEGRAM_BOT_TOKEN');
		$chatId = env('TELEGRAM_CHAT_ID');
		
		if (!$botToken || !$chatId) {
			\Log::error('Telegram credentials not set in .env');
			return false;
		}
		
		try {
			$response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
				'chat_id' => $chatId,
				'text' => $message,
				'parse_mode' => 'HTML'
			]);
			
			return $response->successful();
		} catch (\Exception $e) {
			\Log::error('Telegram send error: ' . $e->getMessage());
			return false;
		}
	}
}