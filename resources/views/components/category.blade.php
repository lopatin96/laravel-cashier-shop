<div class="space-y-6 mt-6">
    <div class="bg-white sm:rounded-lg shadow-sm">
        <div>
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    {{ __("laravel-cashier-shop::specific.categories.$category.title") }}
                </h2>

                <p class="mt-1 text-gray-500 leading-tight">
                    {{ __("laravel-cashier-shop::specific.categories.$category.description") }}
                </p>
            </div>

            <div class="px-6 pb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-x-4 gap-y-12 mt-3">
                    @foreach($products as $product)
                        @include('laravel-cashier-shop::components.product', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
