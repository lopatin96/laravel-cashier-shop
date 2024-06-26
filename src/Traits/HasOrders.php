<?php

namespace Atin\LaravelCashierShop\Traits;


use Atin\LaravelCashierShop\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrders
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)
            ->withTrashed();
    }
}