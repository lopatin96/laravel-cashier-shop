<?php

namespace Atin\LaravelCashierShop\Http\Controllers;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Enums\ProductStatus;
use Atin\LaravelCashierShop\Models\Order;
use Atin\LaravelCashierShop\Models\Product;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    public function index()
    {
        return view('laravel-cashier-shop::shop.index', [
            'productsByCategory' => Product::status(ProductStatus::Deployed)
                ->orderBy('sort_order')
                ->get()
                ->reduce(static function($carry, $item) {
                    $carry[$item->category][] = $item;

                    return $carry;
                }),
            'paidOrders' => auth()->user()
                ->orders()
                ->with('product')
                ->whereIn('status', [OrderStatus::Completed, OrderStatus::Processed])
                ->latest()
                ->paginate(
                    perPage: 5,
                    pageName: 'orders',
                ),
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

        $productData = [
            'description' => __("laravel-cashier-shop::specific.products.$product->category.$product->name.subtitle"),
            'metadata' => [
//                'category' => 'electronics',
            ]
        ];

        if ($product->image) {
            $productData['images'] = [Storage::disk('s3')->temporaryUrl($product->image, now()->addMinute())];
        }

        $sessionOptions = [
            'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout-cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => ['order_id' => $order->id],
        ];

        try {
            return $request->user()->checkout([[
                'price_data' => [
                    'currency' => $request->user()->getCurrency()->iso_code,
                    'product_data' => array_merge($productData, [
                        'name' => __("laravel-cashier-shop::specific.products.$product->category.$product->name.title"),
                    ]),
                    'unit_amount' => $product->getPrice($request->user()),
                ],
                'quantity' => $quantity,
            ]], $sessionOptions);
        } catch (IncompletePayment) {
            return redirect('/shop')->with([
                'flash.banner' => __('An error has occurred. Please try again after some time.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }
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

        return redirect('/shop?status=success')->with([
            'flash.banner' => __('laravel-cashier-shop::common.alerts.success'),
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
