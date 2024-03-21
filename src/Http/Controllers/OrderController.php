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

        $order = Order::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);

        if (! $order) {
            return; // todo: info for user
        }

        $quantity = 1;

        return $request->user()->checkout([
            $product->price_id => $quantity
        ], [
            'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout-cancel'),
            'metadata' => ['order_id' => $order->id],
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId === null) {
            return;
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return;
        }

        $orderId = $session['metadata']['order_id'] ?? null;

        $order = Order::findOrFail($orderId);

        $order->update(['status' => OrderStatus::Completed]);

        return 'ok';
        return view('checkout-success', ['order' => $order]);

        return view('laravel-blog::posts.index', [
            'posts' => Post::getPublished()
                ->paginate(),
        ]);
    }

    public function cancel()
    {
        return 'cancel';
        return view('laravel-blog::posts.index', [
            'posts' => Post::getPublished()
                ->paginate(),
        ]);
    }
}
