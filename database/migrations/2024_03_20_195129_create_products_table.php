<?php

use Atin\LaravelCashierShop\Enums\ProductStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', static function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('price_id')->unique();
            $table->string('category');
            $table->string('status')->default(ProductStatus::Design);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
