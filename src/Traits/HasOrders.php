<?php

namespace Atin\LaravelCashierShop\Traits;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrders
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)
            ->withTrashed();
    }

    public function getTotalProcessedOrderCount(): int
    {
        return $this->orders->where('status', OrderStatus::Processed)->count();
    }

    public function getTotalProcessedOrderAmountInCents(): int
    {
        $amountInCents = 0;

        foreach($this->orders->where('status', OrderStatus::Processed) as $order) {
            if (property_exists($order->log, 'amount') && property_exists($order->log, 'currency')) {
                $amountInCents += $order->quantity * $order->log->amount / config('laravel-cashier-shop.exchange_rates_for_usd')[$order->log->currency];
            }
        }

        return $amountInCents;
    }
}
