<?php

namespace Atin\LaravelCashierShop\Helpers;

use Atin\LaravelCashierShop\Models\Currency;
use InvalidArgumentException;

class CurrencyHelper
{
    public static function getCurrencyByCountryCode(string $countryCode): Currency
    {
        return Currency::whereJsonContains('country_codes', $countryCode)->first()
            ?? Currency::where('iso_code', 'USD')->first();
    }

    public static function convertAmount(int $amount, string $fromCurrency, string $toCurrency): int
    {
        $exchangeRates = config('laravel-cashier-shop.exchange_rates_for_usd');

        if (! isset($exchangeRates[$fromCurrency], $exchangeRates[$toCurrency])) {
            throw new InvalidArgumentException('Exchange rate for one or both currencies is not defined.');
        }

        $amountInUsd = $amount / $exchangeRates[$fromCurrency];

        return $amountInUsd * $exchangeRates[$toCurrency];
    }
}