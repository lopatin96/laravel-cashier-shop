<?php

namespace Atin\LaravelCashierShop\Services\PricingService;

class StripePricingService extends PricingService
{
    public function getPrice(): int
    {
        return 1;
    }
}