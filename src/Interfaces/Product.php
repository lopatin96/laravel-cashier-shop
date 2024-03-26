<?php

namespace Atin\LaravelCashierShop\Interfaces;

use Atin\LaravelCashierShop\Models\Order;
use App\Models\User;

interface Product
{
    public function process(Order $order): void;

    public function canBePurchased(User $user): bool;
}
