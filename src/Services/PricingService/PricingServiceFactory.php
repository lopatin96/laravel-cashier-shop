<?php

namespace Atin\LaravelCashierShop\Services\PricingService;

use Atin\LaravelCashierShop\Enums\Currency;
use Atin\LaravelCashierShop\Models\Product;

class PricingServiceFactory
{
    public static function make(Product $product, Currency $currency): PricingService
    {
        return (new StripePricingService($product, $currency));
    }
}