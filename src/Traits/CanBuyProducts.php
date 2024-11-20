<?php

namespace Atin\LaravelCashierShop\Traits;


use Atin\LaravelCashierShop\Models\Currency;
use Atin\LaravelCashierShop\Helpers\CurrencyHelper;

trait CanBuyProducts
{
    public function getCurrency(): Currency
    {
        return CurrencyHelper::getCurrencyByCountryCode($this->country ?? 'us');
    }
}