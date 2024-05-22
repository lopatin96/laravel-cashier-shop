<?php

namespace Atin\LaravelCashierShop\Interfaces;

use Atin\LaravelCashierShop\Models\Order;
use App\Models\User;

interface Product
{
    public function process(Order $order): void;

    public function isListed(User $user): bool;

    public function isPurchasable(User $user): bool;
}
