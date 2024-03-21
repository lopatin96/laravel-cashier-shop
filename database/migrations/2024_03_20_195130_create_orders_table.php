<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Atin\LaravelCashierShop\Enums\OrderStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', static function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->string('status')->default(OrderStatus::Incomplete);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
