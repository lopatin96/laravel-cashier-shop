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
        <p class="text-center">
            {{ __('No products yet') }}
        </p>
    @endif

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const focuses = urlParams.get('focus');

        if (focuses) {
            const keyframes = [
                { backgroundColor: "#f0fdf4" },
                { transform: "scale(1.05)" },
                { backgroundColor: "#bbf7d0" },
                { transform: "scale(1)" },
                { backgroundColor: "#f0fdf4" },
            ];

            const options = {
                duration: 1000,
                iterations: 3,
            };

            focuses.split(",").forEach(function(focus) {
                const product = document.querySelector("[data-product='" + focus + "'] [data-product-body]");

                if (product) {
                    product.animate(keyframes, options);

                    window.scrollBy({
                        top: product.getBoundingClientRect().top -50,
                        behavior: 'smooth',
                    });
                }


            });
        }
    </script>
</x-shop-layout>
