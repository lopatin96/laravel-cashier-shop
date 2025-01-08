<?php

namespace Atin\LaravelCashierShop\Interfaces;

use App\Models\User;
use Atin\LaravelCashierShop\Models\Order;

abstract class Product
{
    public function __construct(
        protected User $user
    ) {}

    abstract public function process(Order $order): void;

    abstract public function isListed(): bool;

    abstract public function isPurchasable(): bool;

    abstract public function getPrice(): int;

    abstract public function getCrossedPrice(): ?int;
}
