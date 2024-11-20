<?php

namespace Atin\LaravelCashierShop\Models;

use Atin\LaravelCashierShop\Enums\CurrencyDecimalType;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'decimal_type' => CurrencyDecimalType::class,
        'country_codes' => 'array',
    ];
}
