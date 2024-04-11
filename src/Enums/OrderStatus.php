<?php

namespace Atin\LaravelCashierShop\Enums;

enum OrderStatus: string
{
    case Incomplete = 'incomplete';

    case Completed = 'completed';

    case Processed = 'processed';

    case Canceled = 'canceled';
}
