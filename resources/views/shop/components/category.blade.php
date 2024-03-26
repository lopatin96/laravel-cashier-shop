<div class="space-y-6 mt-6">
    <div class="bg-white sm:rounded-lg shadow-sm overflow-hidden">
        <div>
            <div class="flex justify-between">
                <h2 class="pl-6 pt-6 text-xl font-semibold text-gray-700">
                    {{ __("laravel-cashier-shop::shop.categories.$category.title") }}
                </h2>
            </div>

            <div class="px-6 pb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-x-4 gap-y-12 mt-3">
                    @foreach($products as $product)
                        @include('laravel-cashier-shop::shop.components.product', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>