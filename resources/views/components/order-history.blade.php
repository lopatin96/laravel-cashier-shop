@if($orders->isNotEmpty())
    <x-laravel-cashier-shop::basic-section title="{{ __('laravel-cashier-shop::common.order-history.title') }}">
        <table class="min-w-full text-center font-light mt-2">
            <thead class="border-b bg-neutral-50 font-medium dark:border-neutral-500 dark:text-neutral-800">
            <tr>
                <th class="px-4 py-3 uppercase">{{ __('laravel-cashier-shop::common.order-history.product') }}</th>
                <th class="px-4 py-3 uppercase">{{ __('laravel-cashier-shop::common.order-history.date') }}</th>
            </tr>
            </thead>
            <tbody class="text-sm">
            @foreach($orders as $order)
                <tr class="border-b">
                    <td class="px-4 py-3">
                        {{ __('laravel-cashier-shop::specific.products.' . $order->product->category . '.' . $order->product->name . '.title') }}
                        @if($order->quantity > 1) (×{{ $order->quantity }})@endif
                    </td>
                    <td class="px-4 py-3">
                        {{ $order->created_at->diffForHumans() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if ($orders->hasPages())
            <div class="mt-5">
                {{ $orders->links() }}
            </div>
        @endif
    </x-laravel-cashier-shop::basic-section>
@endif