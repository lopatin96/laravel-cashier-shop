<?php

namespace Atin\LaravelCashierShop\Enums;

enum OrderStatus: string
{
    case Incomplete = 'incomplete';

    case Completed = 'completed';
}
