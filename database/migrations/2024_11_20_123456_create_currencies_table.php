<?php

use Atin\LaravelCashierShop\Enums\CurrencyDecimalType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('iso_code', 3)->unique();
            $table->string('decimal_type');
            $table->integer('min_charge_amount')->nullable();
            $table->json('country_codes')->nullable();
            $table->timestamps();
        });

        $currencies = [
            [
                'iso_code' => 'USD',
                'decimal_type' => CurrencyDecimalType::TWO_DECIMAL,
                'min_charge_amount' => 50,
                'country_codes' => json_encode(['us']),
            ],
            [
                'iso_code' => 'EUR',
                'decimal_type' => CurrencyDecimalType::TWO_DECIMAL,
                'min_charge_amount' => 50,
                'country_codes' => json_encode(['de', 'fr', 'it', 'es', 'pt', 'nl', 'be', 'at', 'fi', 'gr', 'ie', 'lu', 'sk', 'si', 'ee', 'lv', 'lt', 'cy', 'mt', 'mc', 'ad', 'sm', 'va', 'me', 'xk']),
            ],
            [
                'iso_code' => 'PLN',
                'decimal_type' => CurrencyDecimalType::TWO_DECIMAL,
                'min_charge_amount' => 200,
                'country_codes' => json_encode(['pl']),
            ],
            [
                'iso_code' => 'UAH',
                'decimal_type' => CurrencyDecimalType::TWO_DECIMAL,
                'min_charge_amount' => null,
                'country_codes' => json_encode(['ua']),
            ],
            [
                'iso_code' => 'RUB',
                'decimal_type' => CurrencyDecimalType::TWO_DECIMAL,
                'min_charge_amount' => null,
                'country_codes' => json_encode(['ru']),
            ],
            [
                'iso_code' => 'JPY',
                'decimal_type' => CurrencyDecimalType::ZERO_DECIMAL,
                'min_charge_amount' => 50,
                'country_codes' => json_encode(['jp']),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
