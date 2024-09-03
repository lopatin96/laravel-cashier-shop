<div>
    <h1 class="px-6 sm:px-0 text-2xl font-semibold text-gray-700">
        {{ __('laravel-cashier-shop::shop.info-title') }}
    </h1>

    @if(auth()->user()->subscribed())
        <div class="mt-6">
            <div>
                <div class="px-6 py-4 bg-gray-200 border border-gray-300 sm:rounded-lg shadow-sm">
                    <div class="max-w-3xl text-sm text-gray-600">
                        {!! __('laravel-cashier-shop::shop.info-text-1') !!}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mt-6">
            <div>
                <div class="px-6 py-4 bg-gray-200 border border-gray-300 sm:rounded-lg shadow-sm">
                    <div class="max-w-3xl text-sm text-gray-600">
                        {!! __('laravel-cashier-shop::shop.info-text-0') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>