<x-shop-layout>
    @php
        $country = auth()->user()->country ?? 'us';
        $locale = auth()->user()->locale ?? 'en';
    @endphp

    <x-banner />

    @if($productsByCategory)
        @foreach($productsByCategory as $category => $products)
            @include('laravel-cashier-shop::shop.components.category', [
                'category' => $category,
                'products' => $products,
            ])
        @endforeach
    @else
        {{ __('No products yet') }}
    @endif
</x-shop-layout>
