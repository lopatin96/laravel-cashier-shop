<?php

namespace Atin\LaravelCashierShop\Http\Controllers;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Enums\ProductStatus;
use Atin\LaravelCashierShop\Models\Product;
use Atin\LaravelCashierShop\Services\PaymentService\FreekassaPaymentService;
use Atin\LaravelCashierShop\Services\PaymentService\PaymentService;
use Atin\LaravelCashierShop\Services\PaymentService\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(Request $request)
    {
        $this->paymentService = auth()->id() < 10 ? new FreekassaPaymentService($request) : new StripePaymentService($request);
    }

    public function index(): View
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

    public function checkout(Product $product, int $quantity = 1): RedirectResponse
    {
        if ($response = $this->paymentService->checkProduct($product)) {
            return $response;
        }

        return $this->paymentService->checkout($product, $quantity);
    }

    public function success(): RedirectResponse
    {
        return $this->paymentService->success();
    }

    public function cancel(): RedirectResponse
    {
        return $this->paymentService->cancel();
    }
}
