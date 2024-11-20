<?php

namespace Atin\LaravelCashierShop\Services\PricingService;

use Atin\LaravelCashierShop\Enums\Currency;
use Atin\LaravelCashierShop\Models\Product;

abstract class PricingService
{
    public function __construct(
        protected Product $product,
        protected Currency $currency,
    )
    {
    }

    abstract public function getPrice(): int;
}