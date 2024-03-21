<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isIncomplete(): bool
    {
        return $this->status === OrderStatus::Incomplete;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function isProcessed(): bool
    {
        return $this->status === OrderStatus::Processed;
    }

    public function isCanceled(): bool
    {
        return $this->status === OrderStatus::Canceled;
    }

    public static function boot()
    {
        parent::boot();

        static::updated(static function (Order $order) {
            if (
                $order->product->model
                && $order->wasChanged('status')
                && $order->getOriginal('status') === OrderStatus::Incomplete
                && $order->isCompleted()
            ) {
                (new \ReflectionClass('App\\Products\\'.$order->product->model))
                    ->newInstanceWithoutConstructor()
                    ->run($order);
            }
        });
    }
}
