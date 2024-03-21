<x-laravel-affiliate-program::basic-section title="{{ __('monetization.payouts-title') }}">
    @foreach($products as $product)
        {{ $product['price_id'] }}
    @endforeach
</x-laravel-affiliate-program::basic-section>
