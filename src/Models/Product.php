<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function getRouteKeyName() {
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

    public function scopeStatus($query, ProductStatus $productStatus): void
    {
        $query->where('status', $productStatus);
    }
}
