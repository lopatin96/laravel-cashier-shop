@if($productsByCategory)
    @foreach($productsByCategory as $category => $products)
        @include('laravel-cashier-shop::components.category', [
            'category' => $category,
            'products' => $products,
        ])
    @endforeach
@else
    <p class="text-center">
        {{ __('No products yet') }}
    </p>
@endif