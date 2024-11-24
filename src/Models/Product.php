<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\CurrencyDecimalType;
use Atin\LaravelCashierShop\Enums\ProductStatus;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Nova\Actions\Actionable;
use Veelasky\LaravelHashId\Eloquent\HashableId;
use App\Models\User;
use Illuminate\Support\Number;

class Product extends Model
{
    use Actionable, SoftDeletes, HashableId;

    protected $shouldHashPersist = true;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
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
     * @throws Exception
     */
    public function instance(User $user): ?object
    {
        if (! $this->model) {
            return null;
        }

        $className = 'App\\Products\\' . $this->model;

        if (! class_exists($className)) {
            throw new \Exception("Class $className does not exist");
        }

        return (new \ReflectionClass($className))->newInstanceArgs([$user]);
    }

    public function isListed(User $user): bool
    {
        return $this->instance($user)?->isListed() ?? true;
    }

    public function isPurchasable(User $user): bool
    {
        return $this->instance($user)?->isPurchasable() ?? true;
    }

    public function getPrice(User $user): int
    {
        return $this->instance($user)?->getPrice();
    }

    public function getCrossedPrice(User $user): ?int
    {
        return $this->instance($user)?->getCrossedPrice();
    }

    public function getDisplayPrice(User $user): string
    {
        $currency = $user->getCurrency();

        return preg_replace(
            '/\.00\s?$/',
            '',
            Number::currency(
                $currency->decimal_type === CurrencyDecimalType::TWO_DECIMAL
                    ? $this->getPrice($user) / 100
                    : $this->getPrice($user),
                in: $currency->iso_code,
                locale: $user->locale
            )
        );
    }

    public function getDisplayCrossedPrice(User $user): ?string
    {
        if ($crossedPrice = $this->getCrossedPrice($user)) {
            $currency = $user->getCurrency();

            return preg_replace(
                '/\.00\s?$/',
                '',
                Number::currency(
                    $currency->decimal_type === CurrencyDecimalType::TWO_DECIMAL
                        ? $crossedPrice / 100
                        : $crossedPrice,
                    in: $currency->iso_code,
                    locale: $user->locale
                )
            );
        }

        return null;
    }
}
