<?php

namespace Atin\LaravelCashierShop\Database\Seeders;

use Atin\LaravelCashierShop\Enums\ProductStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'price_id' => 'price_1OwRWUDqujTYBNKvHtyBzOB1',
                'status' => ProductStatus::Deployed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('products')->insert($data);
    }
}
