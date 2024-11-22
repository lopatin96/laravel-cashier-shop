@php
    $isListed = $product->isListed(auth()->user());

    $isPurchasable = $isListed && $product->isPurchasable(auth()->user());
@endphp

@if($isListed)
    <div
        data-product="{{ $product->name }}"
        class="space-y-3 newspaper"
    >
        <div
            data-product-body
            class="bg-gray-100 rounded-lg"
        >
            <div class="flex justify-between space-x-2 px-5 py-3">
                <p class="text-gray-800 font-semibold leading-5">
                    {{ __("laravel-cashier-shop::specific.products.$product->category.$product->name.title") }}
                </p>
                <x-laravel-ui-components::tooltip text="{{ __('laravel-cashier-shop::specific.products.'. $product->category . '.' . $product->name . '.description') }}" />
            </div>

        @if($product->properties->subtitle ?? false)
            <div class="px-5 pb-2">
                <p class="text-gray-500 text-sm leading-4">
                    {{ __("laravel-cashier-shop::specific.products.$product->category.$product->name.subtitle") }}
                </p>
            </div>
        @endif

        <hr class="opacity-90 mx-5">

        @if($isPurchasable)
            <div class="flex justify-center space-x-4 py-3 items-center">
                @if($crossedPrice = $product->getDisplayCrossedPrice(auth()->user()))
                    <span class="text-gray-500 px-1 relative">
                        {{ $crossedPrice }}
                        <span class="absolute left-0 right-0 top-1/2 transform -translate-y-1/2 -rotate-12 h-0.5 bg-red-600 opacity-75"></span>
                    </span>
                @endif
                <span class="text-2xl font-bold">
                    {{ $product->getDisplayPrice(auth()->user()) }}
                </span>
            </div>
        @else
            <div class="flex items-center justify-center space-x-2 py-3 text-gray-500 select-none cursor-default">
                <svg
                    class="shrink-0 size-4"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>

                <p class="text-lg font-semibold">
                    {{ __('Purchased') }}
                </p>

            </div>
        @endif
    </div>

        <div
            class="flex md:flex-col lg:flex-row xl:flex-col justify-between items-end space-x-4 md:space-x-0 lg:space-x-4 xl:space-x-0 md:space-y-2 lg:space-y-0 xl:space-y-2"
            x-data="{
                quantity: 1,
                max_quantity: {{ $product->properties->max_quantity ?? 99 }},
                hashid: '{{ $product->hashid }}',
            }"
        >
            <div>
                @if($isPurchasable && ($product->properties->one_time_purchase ?? false))
                    <p class="text-sm font-semibold text-gray-500">
                        {{ __('Not purchased') }}
                    </p>
                @endif

                @if($isPurchasable && ! ($product->properties->one_time_purchase ?? false))
                    <div class="py-1 px-3 inline-block bg-white border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-x-1.5">
                            <button
                                x-on:click="quantity = quantity > 1 ? parseInt(quantity)-1 : 1"
                                type="button" class="size-6 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-md border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                title="{{ __('laravel-cashier-shop::common.product.quantity.less.tooltip') }}"
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
                                title="{{ __('laravel-cashier-shop::common.product.quantity.more.tooltip') }}"
                            >
                                <svg class="flex-shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            @if($isPurchasable)
                <a
                    :href="'/checkout/' + hashid + '/' + quantity"
                    class="shrink-0"
                >
                    <x-button type="button">
                        {{ __('laravel-cashier-shop::common.buttons.buy.title') }}
                    </x-button>
                </a>
            @endif
        </div>
    </div>
@endif