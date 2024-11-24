@php
    $isListed = $product->isListed(auth()->user());

    $isPurchasable = $isListed && $product->isPurchasable(auth()->user());
@endphp

@if($isListed)
    <div
        data-product="{{ $product->name }}"
        class="space-y-2 newspaper"
    >
        <div
            data-product-body
            class="bg-gray-100 rounded-lg"
        >
            <div class="flex justify-between space-x-2 px-5 py-3">
                <p class="text-gray-800 font-semibold leading-5">
                    {{ __("laravel-cashier-shop::specific.products.$product->category.$product->name.title") }}
                </p>
                <x-laravel-ui-components::tooltip
                    text="{{ __('laravel-cashier-shop::specific.products.'. $product->category . '.' . $product->name . '.description') }}"
                    position="left"
                />
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

        <div class="flex justify-center">
            @if($isPurchasable)
                <a href="{{ '/checkout/' . $product->hashid }}">
                    <x-button type="button">
                        <div class="flex items-center space-x-2">
                            <span>
                                {{ __('laravel-cashier-shop::common.buttons.buy.title') }}
                            </span>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </div>
                    </x-button>
                </a>
            @endif
        </div>
    </div>
@endif