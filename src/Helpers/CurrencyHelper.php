<?php

namespace Atin\LaravelCashierShop\Helpers;

use Atin\LaravelCashierShop\Models\Currency;

class CurrencyHelper
{
    public static function getCurrencyByCountryCode(string $countryCode): Currency
    {
        return Currency::whereJsonContains('country_codes', $countryCode)->first()
            ?? Currency::where('iso_code', 'USD')->first();
    }
}