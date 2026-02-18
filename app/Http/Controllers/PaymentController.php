<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Допустимые тарифы: days => amount в KZT
     */
    private const TARIFFS = [
        5  => 4000,
        10 => 10846,
        30 => 16268,
    ];

    /**
     * Создать платёж и перенаправить на страницу оплаты
     */
    public function createPayment(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Для оплаты необходимо авторизоваться');
        }

        $request->validate([
            'amount'  => 'required|integer',
            'days'    => 'required|integer|in:5,10,30',
            'service' => 'required|string|in:verified_status,top_post',
            'post_id' => 'nullable|integer',
        ]);

        $days = (int) $request->input('days');
        $amount = (int) $request->input('amount');
        $service = $request->input('service');
        $postId = $request->input('post_id');

        // Проверяем соответствие суммы тарифу
        if (!isset(self::TARIFFS[$days]) || self::TARIFFS[$days] !== $amount) {
            return back()->with('error', 'Некорректные параметры тарифа');
        }

        $user = DB::table('users')->where('id', session('user_id'))->first();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Пользователь не найден');
        }

        $orderId = 'order_' . $user->id . '_' . time();

        // Сохраняем заказ в БД
        DB::table('orders')->insert([
            'user_id'  => $user->id,
            'email'    => $user->email,
            'order_id' => $orderId,
            'amount'   => $amount,
            'days'     => $days,
            'post_id'  => $postId,
            'service'  => $service,
            'status'   => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publicKey = config('services.betatransfer.public_key');
        $secretKey = config('services.betatransfer.secret_key');

        $postData = [
            'orderId'       => $orderId,
            'amount'        => (string) $amount,
            'currency'      => 'KZT',
            'paymentSystem' => 'P2R_KZT',
            'urlResult'     => route('payment.callback'),
            'urlSuccess'    => route('payment.success'),
            'urlFail'       => route('payment.fail'),
            'locale'        => 'ru',
            'redirect'      => '1',
            'payerId'       => (string) $user->id,
            'payerEmail'    => $user->email,
            'payerName'     => $user->fio ?? '',
        ];

        // Подпись: md5(все значения параметров + секретный ключ)
        $sign = md5(implode('', $postData) . $secretKey);
        $postData['sign'] = $sign;

        Log::info('Payment request', ['order_id' => $orderId, 'amount' => $amount, 'days' => $days]);

        try {
            $response = Http::timeout(30)
                ->withOptions(['allow_redirects' => false])
                ->asForm()
                ->post(
                    'https://merchant.betatransfer.io/api/payment?token=' . $publicKey,
                    $postData
                );

            $httpCode = $response->status();
            $body = $response->body();

            Log::info('Payment response', [
                'http_code' => $httpCode,
                'body' => mb_substr($body, 0, 500),
            ]);

            // Если редирект — берём URL из заголовка Location
            if ($httpCode >= 300 && $httpCode < 400) {
                $location = $response->header('Location');
                if ($location) {
                    return redirect($location);
                }
            }

            // Если JSON-ответ с url
            $result = $response->json();
            if (isset($result['url'])) {
                return redirect($result['url']);
            }

            // Если HTML — пробуем найти URL оплаты в теле ответа
            if ($httpCode === 200 && str_contains($body, 'processingv2.betatransfer.io')) {
                if (preg_match('/"url"\s*:\s*"(https:\/\/processingv2\.betatransfer\.io\/[^"]+)"/', $body, $matches)) {
                    return redirect($matches[1]);
                }
            }

            // Обновляем статус заказа при ошибке
            DB::table('orders')->where('order_id', $orderId)->update([
                'status'       => 'failed',
                'payment_data' => json_encode(['error' => mb_substr($body, 0, 1000), 'http_code' => $httpCode]),
                'updated_at'   => now(),
            ]);

            return back()->with('error', 'Ошибка при создании платежа. Попробуйте позже.');

        } catch (\Exception $e) {
            Log::error('Payment request failed: ' . $e->getMessage());

            DB::table('orders')->where('order_id', $orderId)->update([
                'status'       => 'failed',
                'payment_data' => json_encode(['exception' => $e->getMessage()]),
                'updated_at'   => now(),
            ]);

            return back()->with('error', 'Ошибка соединения с платёжной системой. Попробуйте позже.');
        }
    }

    /**
     * Callback от платёжной системы (server-to-server)
     * POST /result_url_new
     */
    public function callback(Request $request)
    {
        Log::info('Payment callback received', $request->all());

        $secretKey = config('services.betatransfer.secret_key');

        $orderId      = $request->input('orderId');
        $amount       = $request->input('amount');
        $receivedSign = $request->input('sign');

        // Webhook signature: md5(amount + orderId + secret)
        $expectedSign = md5($amount . $orderId . $secretKey);

        if ($receivedSign !== $expectedSign) {
            Log::warning('Payment callback: invalid signature', [
                'order_id' => $orderId,
                'expected' => $expectedSign,
                'received' => $receivedSign,
            ]);
            return response('Invalid signature', 400);
        }

        // Находим заказ
        $order = DB::table('orders')->where('order_id', $orderId)->first();

        if (!$order) {
            Log::warning('Payment callback: order not found', ['order_id' => $orderId]);
            return response('Order not found', 404);
        }

        // Если заказ уже оплачен — не обрабатываем повторно
        if ($order->status === 'paid') {
            Log::info('Payment callback: order already paid', ['order_id' => $orderId]);
            return response('OK', 200);
        }

        // Сохраняем данные колбэка (пока processing, чтобы при ошибке можно было повторить)
        DB::table('orders')->where('order_id', $orderId)->update([
            'payment_data' => json_encode($request->all()),
            'updated_at'   => now(),
        ]);

        // Активируем услугу
        try {
            if ($order->service === 'verified_status') {
                $user = DB::table('users')->where('id', $order->user_id)->first();

                if ($user) {
                    $baseDate = ($user->prov == 1 && $user->prov_date && Carbon::parse($user->prov_date)->isFuture())
                        ? Carbon::parse($user->prov_date)
                        : Carbon::now();

                    $newProvDate = $baseDate->addDays($order->days);

                    DB::table('users')->where('id', $order->user_id)->update([
                        'prov'      => 1,
                        'prov_date' => $newProvDate,
                    ]);

                    Log::info('Verified status activated', [
                        'user_id'   => $order->user_id,
                        'days'      => $order->days,
                        'prov_date' => $newProvDate->toDateTimeString(),
                    ]);
                }
            }

            if ($order->service === 'top_post' && $order->post_id) {
                $dateEnd = Carbon::now()->addDays($order->days);

                // Сбрасываем count_view у всех топ-объявлений
                DB::table('top_post')->update(['count_view' => 0]);

                // Проверяем, есть ли уже этот пост в топе
                $existing = DB::table('top_post')->where('id_post', $order->post_id)->first();

                if ($existing) {
                    $baseDate = Carbon::parse($existing->date_end)->isFuture()
                        ? Carbon::parse($existing->date_end)
                        : Carbon::now();

                    DB::table('top_post')->where('id_post', $order->post_id)->update([
                        'date_end'   => $baseDate->addDays($order->days),
                        'count_view' => 0,
                    ]);
                } else {
                    DB::table('top_post')->insert([
                        'id_post'    => $order->post_id,
                        'count_view' => 0,
                        'date_end'   => $dateEnd,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                Log::info('Top post activated', [
                    'post_id'  => $order->post_id,
                    'days'     => $order->days,
                    'date_end' => $dateEnd->toDateTimeString(),
                ]);
            }

            // Всё прошло — ставим paid
            DB::table('orders')->where('order_id', $orderId)->update([
                'status'     => 'paid',
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Service activation failed', [
                'order_id' => $orderId,
                'service'  => $order->service,
                'post_id'  => $order->post_id ?? null,
                'error'    => $e->getMessage(),
            ]);

            DB::table('orders')->where('order_id', $orderId)->update([
                'status'     => 'failed',
                'updated_at' => now(),
            ]);

            return response('Activation error: ' . $e->getMessage(), 500);
        }

        return response('OK', 200);
    }

    /**
     * Страница успешной оплаты
     */
    public function success(Request $request)
    {
        return view('payment.success');
    }

    /**
     * Страница неудачной оплаты
     */
    public function fail(Request $request)
    {
        return view('payment.fail');
    }
}
