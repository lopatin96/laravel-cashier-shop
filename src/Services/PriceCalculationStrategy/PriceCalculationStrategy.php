<?php

namespace Atin\LaravelCashierShop\Services\PriceCalculationStrategy;

use App\Models\User;
use Atin\LaravelCashierShop\Models\Product;

interface PriceCalculationStrategy
{
    public function getPrice(Product $product, User $user): int;
}