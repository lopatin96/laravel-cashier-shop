<?php

use Atin\LaravelCashierShop\Http\Controllers\OrderController;

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/shop', [OrderController::class, 'index'])
        ->name('shop');

    Route::get('/checkout/success', [OrderController::class, 'success'])
        ->name('checkout-success');

    Route::get('/checkout/cancel', [OrderController::class, 'cancel'])
        ->name('checkout-cancel');

    Route::get('/checkout/{product}/{quantity?}', [OrderController::class, 'checkout'])
        ->name('checkout');
});