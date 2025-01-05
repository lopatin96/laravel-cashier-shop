<?php

namespace Atin\LaravelCashierShop\Services\PaymentService;

class PaymentServiceFactory
{
    public static function make(): PaymentService
    {
        return new StripePaymentService(request());
    }
}