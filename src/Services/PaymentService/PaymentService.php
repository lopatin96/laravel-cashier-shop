<?php

namespace Atin\LaravelCashierShop\Services\PaymentService;

use Atin\LaravelCashierShop\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

abstract class PaymentService
{
    public function __construct(
        protected Request $request
    ) {}

    abstract public function checkout(Product $product, int $quantity): RedirectResponse;

    abstract public function success(): RedirectResponse;

    abstract public function cancel(): RedirectResponse;

    final public function checkProduct(Product $product): RedirectResponse|null
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

        return null;
    }

    final protected function getLog(): array
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