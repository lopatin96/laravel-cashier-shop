<?php

namespace Atin\LaravelCashierShop\Products;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Models\Order;

abstract class Product
{
    public function run(Order $order): void
    {
        if ($order->isCompleted()) {
            $this->process($order);

            $order->update([
                'status' => OrderStatus::Processed,
            ]);
        }
    }

    abstract protected function process(Order $order): void;
}
