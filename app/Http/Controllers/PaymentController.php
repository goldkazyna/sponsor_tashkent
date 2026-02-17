<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Тестовая страница оплаты
     */
    public function test()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Для оплаты необходимо авторизоваться');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        return view('payment.test', compact('user'));
    }

    /**
     * Создать платёж и перенаправить на страницу оплаты
     */
    public function createPayment(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();

        $publicKey = config('services.betatransfer.public_key');
        $secretKey = config('services.betatransfer.secret_key');

        $orderId = 'order_' . $user->id . '_' . time();
        $amount = $request->input('amount', '100');
        $currency = 'KZT';

        // Параметры платежа
        $params = [
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
        ];

        // Подпись: md5(amount + currency + orderId + secretKey)
        $sign = md5($amount . $currency . $orderId . $secretKey);

        $postData = [
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'urlResult' => route('payment.callback'),
            'urlSuccess' => route('payment.success'),
            'urlFail' => route('payment.fail'),
            'locale' => 'ru',
            'redirect' => '1',
            'payerId' => (string) $user->id,
            'payerEmail' => $user->email,
            'payerName' => $user->fio ?? '',
            'sign' => $sign,
        ];

        Log::info('Payment request', $postData);

        // Запрос к API
        $response = Http::asForm()->post(
            'https://merchant.betatransfer.io/api/payment?token=' . $publicKey,
            $postData
        );

        $result = $response->json();

        Log::info('Payment response', $result ?? []);

        if (isset($result['url'])) {
            // Сохраняем заказ в БД (опционально)
            // DB::table('orders')->insert([...]);

            return redirect($result['url']);
        }

        return back()->with('error', 'Ошибка создания платежа: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Callback от платёжной системы (server-to-server)
     * POST /result_url_new
     */
    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());

        // TODO: Проверка подписи
        // TODO: Активация статуса пользователя

        return response('OK', 200);
    }

    /**
     * Страница успешной оплаты
     * GET /success_url
     */
    public function success(Request $request)
    {
        return view('payment.success');
    }

    /**
     * Страница неудачной оплаты
     * GET /fail
     */
    public function fail(Request $request)
    {
        return view('payment.fail');
    }
}
