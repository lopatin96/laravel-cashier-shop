<div class="space-y-3">
    <div class="bg-gray-100 rounded-lg">
        <div class="flex items-center justify-between px-5 py-3">
            <p class="text-gray-800 font-semibold">
                {{ __("laravel-cashier-shop::shop.products.{$product['category']}.{$product['name']}.title") }}
            </p>
            <x-laravel-ui-components::tooltip text="{{ __('laravel-cashier-shop::shop.products.'. $product['category'] . '.' . $product['name'] . '.description') }}" />
        </div>

        <hr class="opacity-90 mx-5">

        <p class="text-xl font-bold text-center py-3">
            {{
                Number::currency(
                    array_key_exists($country, $product['prices']) ? $product['prices'][$country] : $product['prices']['us'],
                    in: array_key_exists($country, $product['prices']) && array_key_exists($country, config("laravel-cashier-shop.country_to_currency")) ? config("laravel-cashier-shop.country_to_currency")[$country] : 'USD',
                    locale: $locale,
                )
            }}
        </p>
    </div>

    <div
        class="flex md:flex-col lg:flex-row xl:flex-col justify-between space-x-4 md:space-x-0 lg:space-x-4 xl:space-x-0 md:space-y-2 lg:space-y-0 xl:space-y-2"
        x-data="{
            quantity: 1,
            max_quantity: {{ $product['properties']->max_quantity ?? 99 }},
            hashid: '{{ $product['hashid'] }}',
         }"
    >
        <div>
            @unless($product['properties']->one_time ?? false)
                <div class="py-1 px-3 inline-block bg-white border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-x-1.5">
                        <button
                            x-on:click="quantity = quantity > 1 ? parseInt(quantity)-1 : 1"
                            type="button" class="size-6 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                        >
                            <svg class="flex-shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                        </button>
                        <input
                            class="p-0 w-6 bg-transparent border-0 text-gray-800 text-center focus:ring-0"
                            type="text"
                            x-model="quantity"
                        >
                        <button
                            x-on:click="quantity = quantity < max_quantity ? parseInt(quantity)+1 : parseInt(max_quantity)"
                            type="button" class="size-6 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                        >
                            <svg class="flex-shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <a
            :href="'/checkout/' + hashid + '/' + quantity"
            class="shrink-0"
        >
            <x-button type="button">
                {{ __('Buy Now') }}
            </x-button>
        </a>
    </div>
</div>