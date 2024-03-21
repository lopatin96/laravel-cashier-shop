<x-laravel-affiliate-program::basic-section title='{{ __("laravel-cashier-shop::shop.categories.$category.title") }}'>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-4 mt-3">
        @foreach($products as $product)
            @include('laravel-cashier-shop::shop.components.product', ['product' => $product])
        @endforeach
    </div>
</x-laravel-affiliate-program::basic-section>
