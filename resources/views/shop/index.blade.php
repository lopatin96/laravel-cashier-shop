<x-shop-layout>
    @php
        $country = auth()->user()->country ?? 'us';
        $locale = auth()->user()->locale ?? 'en';
    @endphp

    <x-banner />

    @include('laravel-cashier-shop::components.products', ['productsByCategory' => $productsByCategory])

    @include('laravel-cashier-shop::components.order-history', ['orders' => $paidOrders])

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

            let isFirst = true;

            focuses.split(",").forEach(function(focus) {
                const product = document.querySelector("[data-product='" + focus + "'] [data-product-body]");

                if (!product) {
                    return;
                }

                product.animate(keyframes, options);

                if (isFirst) {
                    window.scrollBy({
                        top: product.getBoundingClientRect().top -50,
                        behavior: 'smooth',
                    });

                    isFirst = false;
                }
            });
        }
    </script>
</x-shop-layout>
