<?php

namespace Atin\LaravelCashierShop\Services\PaymentService;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Models\Order;
use Atin\LaravelCashierShop\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FreekassaPaymentService extends PaymentService
{
    private int $merchantId = 57713;
    private string $secret = '(Em?g{]{u,)m}rQ';
    private string $merchantSecret = 'Zf8dGzuhd%w*ziP';
    private string $apiKey = 'Zf8dGzuhd%w*ziP';
    private array $allowedIps = [
        '168.119.157.136',
        '168.119.60.227',
        '178.154.197.79',
        '51.250.54.238',
    ];

    public function checkout(Product $product, int $quantity): RedirectResponse
    {
        $quantity = ($product->properties->one_time_purchase ?? false)
            ? 1
            : min($product->properties->max_quantity ?? 99, max(1, $quantity));

        $currency = $this->request->user()->getCurrency();
        $price = $product->getPrice($this->request->user());

        $order = Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'log' =>  array_merge($this->getLog(), [
                'amount' => $price,
                'currency' => $currency->iso_code,
            ]),
        ]);

        if (! $order) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        try {
            // Данные для запроса
            $order_amount = $price / 100;
            $sign = md5($this->merchantId.':'.$order_amount.':'.$this->secret.':'.$currency->iso_code.':'.$order->id);

            // Формируем URL для редиректа
            $paymentUrl = 'https://pay.freekassa.com/';
            $queryParams = http_build_query([
                'm' => $this->merchantId,
                'oa' => $order_amount,
                'o' => $order->id,
                's' => $sign,
                'currency' => $currency->iso_code,
                'i' => 1,
                'lang' => 'ru',
                'us_order_id' => $order->id,
            ]);

            // Выполняем редирект на сайт оплаты с параметрами
            return redirect()->away($paymentUrl . '?' . $queryParams);
        } catch (IncompletePayment) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }
    }

    public function success(): RedirectResponse
    {
        $sessionId = $this->request->get('session_id');

        if ($sessionId === null) {
            activity()
                ->causedBy(auth()->user())
                ->log('shop:success:session-is-null');

            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            activity()
                ->causedBy(auth()->user())
                ->log('shop:success:payment-status-is-not-paid');

            return redirect('/shop')->with([
                'flash.banner' => __('Something went wrong. Don’t worry, contact our technical support (go to the main page of the site and click on the chat icon in the lower right corner of the screen).'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Completed]);

        return redirect('/shop?status=success')->with([
            'flash.banner' => __('laravel-cashier-shop::common.alerts.success'),
            'flash.bannerStyle' => 'success',
        ]);
    }

    public function cancel(): RedirectResponse
    {
        $sessionId = $this->request->get('session_id');

        if ($sessionId === null) {
            activity()
                ->causedBy(auth()->user())
                ->log('shop:cancel:session-is-null');

            return redirect('/shop');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            activity()
                ->causedBy(auth()->user())
                ->log('shop:cancel:payment-status-is-not-paid');

            return redirect('/shop');
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Canceled]);

        return redirect('/shop');
    }

    public function webhook(): Response
    {
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'log',
            'queue' => 'logging',
            'payload' => json_encode($this->request->all()),
            'exception' => '123',
            'failed_at' => now(),
        ]);

        // Проверка IP
        if (! in_array($this->getIP(), $this->allowedIps)) {
            abort(403, 'Hacking attempt!');
        }

//        // Проверка подписи
//        $sign = md5($this->request->input('MERCHANT_ID') . ':' . $this->request->input('AMOUNT') . ':' . $this->merchantSecret . ':' . $this->request->input('MERCHANT_ORDER_ID'));
//        if ($sign !== $this->request->input('SIGN')) {
//            abort(400, 'Wrong sign');
//        }

        // TODO: Проверить сумму платежа и статус заявки, чтобы избежать повторной обработки.

        // Оплата прошла успешно
        return response('YES', 200);
    }

    private function getIP()
    {
        return $this->request->header('X-Real-IP', $this->request->ip());
    }
}
