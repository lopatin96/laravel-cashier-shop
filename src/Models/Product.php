<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionException;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use App\Models\User;

class Product extends Model
{
    use SoftDeletes, HashableId;

    protected $shouldHashPersist = true;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'prices' => 'array',
        'crossed_prices' => 'array',
        'properties' => 'object',
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function user()
    {
        return $this->belongsTo(Order::class);
    }

    public function isDesign(): bool
    {
        return $this->status === ProductStatus::Design;
    }

    public function isDeployed(): bool
    {
        return $this->status === ProductStatus::Deployed;
    }

    public function isRetired(): bool
    {
        return $this->status === ProductStatus::Retired;
    }

    public function scopeStatus($query, array|ProductStatus $status): void
    {
        $query->whereIn('status', is_array($status) ? $status : [$status]);
    }

    public function scopeWhereModel($query, string $model): void
    {
        $query->where('model', $model);
    }

    /**
     * @throws ReflectionException
     */
    public function instance(): ?object
    {
        if (! $this->model) {
            return null;
        }

        return (new \ReflectionClass('App\\Products\\'.$this->model))
            ->newInstanceWithoutConstructor();
    }

    public function isListed(User $user): bool
    {
        return $this->instance()?->isListed($user) ?? true;
    }

    public function isPurchasable(User $user): bool
    {
        return $this->instance()?->isPurchasable($user) ?? true;
    }
}
