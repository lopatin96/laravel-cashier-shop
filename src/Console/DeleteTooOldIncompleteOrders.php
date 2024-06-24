<?php

namespace Atin\LaravelCashierShop\Console;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Models\Order;

class DeleteTooOldIncompleteOrders
{
    public function __construct(
        protected int $days = 14,
    ) {
    }

    public function __invoke(): void
    {
        Order::status(OrderStatus::Incomplete)
            ->whereDate('created_at', '<', now()->subDays($this->days))
            ->delete();
    }
}
