<?php

namespace Atin\LaravelCashierShop\Services\PriceCalculationStrategy;

use App\Models\User;
use Atin\LaravelCashierShop\Enums\CurrencyDecimalType;
use Atin\LaravelCashierShop\Models\Product;

class BasePriceCalculationStrategy implements PriceCalculationStrategy
{
    private static string $baseCountryCode = 'us';

    public function getPrice(Product $product, User $user): int
    {
        if (is_null($user->country) || $user->country === self::$baseCountryCode) {
            return $product->base_price;
        }

        $currency = $user->getCurrency();

        $amount = $product->base_price
            * config('laravel-cashier-shop.exchange_rates_for_usd')[$currency->iso_code]
            * self::getPPPForCountry($user->country);

        $amount = match($currency->decimal_type) {
            CurrencyDecimalType::ZERO_DECIMAL => ceil($amount / 100),
            CurrencyDecimalType::TWO_DECIMAL => ceil($amount / 100) * 100,
        };

        return max($amount, $currency->min_charge_amount);
    }

    public static function getPPPForCountry(?string $countryCode = null): float
    {
        if (! isset(config('laravel-cashier-shop.country_to_ppp')[$countryCode])) {
            return self::getPPPForCountry(self::$baseCountryCode);
        }

        $gdpCountry = config('laravel-cashier-shop.country_to_ppp')[$countryCode];

        $minGdp = min(array_filter(config('laravel-cashier-shop.country_to_ppp'), fn($value, $key) => $key !== self::$baseCountryCode, ARRAY_FILTER_USE_BOTH));
        $maxGdp = max(array_filter(config('laravel-cashier-shop.country_to_ppp'), fn($value, $key) => $key !== self::$baseCountryCode, ARRAY_FILTER_USE_BOTH));

        return 0.25 + (($gdpCountry - $minGdp) / ($maxGdp - $minGdp)) * (1.75 - 0.25);
    }
}