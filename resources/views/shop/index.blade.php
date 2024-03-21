<x-shop-layout>
    @include('laravel-cashier-shop::shop.components.info')

    @foreach($productsByCategory as $category => $products)
        @include('laravel-cashier-shop::shop.components.category', [
            'category' => $category,
            'products' => $products,
        ])
    @endforeach
</x-shop-layout>
