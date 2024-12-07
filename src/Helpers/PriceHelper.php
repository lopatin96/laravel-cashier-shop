<?php

namespace Atin\LaravelCashierShop\Helpers;

use Atin\LaravelCashierShop\Enums\CurrencyDecimalType;
use App\Models\User;
use Illuminate\Support\Number;

class PriceHelper
{

    public static function formatPrice(User $user, int $price): string
    {
        $currency = $user->getCurrency();

        return preg_replace(
            '/(?<=\d)(\.00|,00)(?!\d)/',
            '',
            Number::currency(
                $currency->decimal_type === CurrencyDecimalType::TWO_DECIMAL
                    ? $price / 100
                    : $price,
                in: $currency->iso_code,
                locale: $user->locale
            )
        );
    }
}
