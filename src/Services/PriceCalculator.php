<?php

namespace Atin\LaravelCashierShop\Services;

use App\Models\User;
use Atin\LaravelCashierShop\Models\Product;
use Atin\LaravelCashierShop\Services\PriceCalculationStrategy\PriceCalculationStrategy;

class PriceCalculator
{
    public function __construct(
        protected Product $product,
        protected User $user,
        protected PriceCalculationStrategy $priceCalculationStrategy,
    )
    {
    }

    public function getPrice(): int
    {
        return $this->priceCalculationStrategy->getPrice($this->product, $this->user);
    }
}