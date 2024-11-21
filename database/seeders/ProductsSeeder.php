<?php

namespace Atin\LaravelCashierShop\Database\Seeders;

use Atin\LaravelCashierShop\Enums\ProductStatus;
use Atin\LaravelCashierShop\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'category' => 'antiplagiarism',
            'name' => '1-extra-check',
            'status' => ProductStatus::Deployed,
            'model' => 'Antiplagiarism1ExtraCheck',
            'base_price' => 1000,
            'properties' => [
                'max_quantity' => 5,
                'subtitle' => true,
            ],
        ]);
    }
}
