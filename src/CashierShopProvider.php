<?php

namespace Atin\LaravelCashierShop;

use Illuminate\Support\ServiceProvider;

class CashierShopProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cashier-shop');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-cashier-shop');

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-cashier-shop');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('/migrations')
        ], 'laravel-cashier-shop-migrations');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/laravel-cashier-shop'),
        ], 'laravel-cashier-shop-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-cashier-shop')
        ], 'laravel-cashier-shop-views');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-cashier-common.php')
        ], 'laravel-cashier-shop-config');
    }
}