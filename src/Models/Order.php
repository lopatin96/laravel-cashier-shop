<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\OrderStatus;
use Atin\LaravelCashierShop\Notifications\NewOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use Atin\LaravelConfigurator\Helpers\ConfiguratorHelper;
use App\Enums\ConfigKey;
use App\Models\User;

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
        'log' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
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

    public function getAmountInCents(): int
    {
        return $this->log->amount / config('laravel-cashier-shop.exchange_rates_for_usd')[$this->log->currency];
    }

    public function scopeStatus($query, array|OrderStatus $status): void
    {
        $query->whereIn('status', is_array($status) ? $status : [$status]);
    }

    public static function boot(): void
    {
        parent::boot();

        static::updating(static function (Order $order) {
            if (
                $order->product->model
                && $order->isDirty('status')
                && $order->getOriginal('status') === OrderStatus::Incomplete
                && $order->isCompleted()
                && ($instance = $order->product->instance($order->user))
            ) {
                $instance->process($order);

                $order->status = OrderStatus::Processed;
            }
        });

        static::updated(static function (Order $order) {
            Notification::send(User::find(1), new NewOrder($order));
            if (
                $order->wasChanged('status')
                && $order->getOriginal('status') === OrderStatus::Incomplete
                && $order->isCompleted()
                && in_array(substr(NewOrder::class, strrpos(NewOrder::class, '\\') + 1), ConfiguratorHelper::getValue(ConfigKey::NotificationNotifyAbout), true)
            ) {
                Notification::send(User::find(1), new NewOrder($order));
            }
        });
    }
}
