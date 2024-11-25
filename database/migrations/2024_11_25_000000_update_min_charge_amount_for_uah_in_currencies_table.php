<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currencies')
            ->where('iso_code', 'UAH')
            ->update(['min_charge_amount' => 2100]);
    }

    public function down(): void
    {
        DB::table('currencies')
            ->where('iso_code', 'UAH')
            ->update(['min_charge_amount' => null]);
    }
};
