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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeStatus($query, OrderStatus $status): void
    {
        $query->where('status', $status);
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
                && ($instance = $order->product->instance())
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
