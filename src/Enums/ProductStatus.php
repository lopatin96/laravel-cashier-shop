<?php

namespace Atin\LaravelCashierShop\Enums;

enum ProductStatus: string
{
    case Design = 'design';

    case Deployed = 'deployed';

    case Retired = 'retired';
}
