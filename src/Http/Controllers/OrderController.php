<?php

namespace Atin\LaravelCashierShop\Http\Controllers;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Enums\ProductStatus;
use Atin\LaravelCashierShop\Models\Order;
use Atin\LaravelCashierShop\Models\Product;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class OrderController extends Controller
{
    public function index()
    {
        return view('laravel-cashier-shop::shop.index', [
            'productsByCategory' => array_reduce(Product::status(ProductStatus::Deployed)->get()->toArray(), static function($carry, $item) {
                $carry[$item['category']][] = $item;

                return $carry;
            })
        ]);
    }

    public function checkout(Request $request, Product $product, int $quantity = 1)
    {
        if (! $product->isDeployed()) {
            return;
        }

        // todo: validate quantity

        $order = Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);

        if (! $order) {
            return; // todo: info for user
        }

        return $request->user()->checkout([
            $product->price_id => $quantity
        ], [
            'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout-cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => ['order_id' => $order->id],
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            // todo: log error
            return redirect('/shop')->with([
                'flash.banner' => __('Произошла ошибка. Повторите пожалуйста еще раз через какое-то время.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            // todo: log error
            return redirect('/shop')->with([
                'flash.banner' => __('Что-то пошло не так. Не волнуйтесь, обратитесь к консультанту (перейдите на сайт и нажмите на иноку сообщения в правом нижнем углу).'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Completed]);

        return redirect('/shop')->with([
            'flash.banner' => __('Оплата прошла успешно.'),
            'flash.bannerStyle' => 'success',
        ]);
    }

    public function cancel(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return redirect('/shop');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect('/shop');
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Canceled]);

        return redirect('/shop');
    }
}
