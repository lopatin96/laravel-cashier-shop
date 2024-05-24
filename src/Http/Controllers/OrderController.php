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
            'productsByCategory' => Product::status(ProductStatus::Deployed)->orderBy('sort_order')->get()->reduce(static function($carry, $item) {
                $carry[$item->category][] = $item;

                return $carry;
            }),
        ]);
    }

    public function checkout(Request $request, Product $product, int $quantity = 1)
    {
        if (! $product->isDeployed()) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        if (! $product->isPurchasable(auth()->user())) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. You have already purchased this product.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $quantity = ($product->properties->one_time_purchase ?? false)
            ? 1
            : min($product->properties->max_quantity ?? 99, max(1, $quantity));

        $order = Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'log' => $this->getLog(),
        ]);

        if (! $order) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        return $request->user()->checkout([
            $product->price_id => $quantity,
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
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            // todo: log error
            return redirect('/shop')->with([
                'flash.banner' => __('Something went wrong. Don’t worry, contact our technical support (go to the main page of the site and click on the chat icon in the lower right corner of the screen).'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Completed]);

        return redirect('/shop')->with([
            'flash.banner' => __('The payment was successful. You have purchased the product.'),
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

    private function getLog(): array
    {
        $log = [];

        if (request()->query()) {
            $log = array_merge($log, request()->query());
        }

        return array_merge($log, [
            'url_previous' => url()->previous(),
        ]);
    }
}
