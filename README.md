# Install
###
Create ```app/View/Components/ShopLayout.php```.
```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ShopLayout extends Component
{
    public function render(): View
    {
        return view('layouts.shop');
    }
}
```

###
Create ```resources/views/layouts/shop.blade.php```.

### Trait
Add ```HasOrders``` trait and cast to User model.

```php
use Atin\LaravelCashierShop\Traits\HasOrders;

class User extends Authenticatable
{
    use HasOrders;

    protected $casts = [
        …
        'shop_visited_at' => 'datetime',
    ];
 
```

### Products
Create ```app/Products``` directory and TestProduct class:

```php
<?php

namespace App\Products;

use App\Models\User;
use Atin\LaravelCashierShop\Interfaces\Product;
use Atin\LaravelCashierShop\Models\Order;

class TestProduct implements Product
{

    public function process(Order $order): void
    {
        // TODO: Implement process() method.
    }

    public function isListed(User $user): bool
    {
        // TODO: Implement isListed() method.
    }

    public function isPurchasable(User $user): bool
    {
        // TODO: Implement isPurchasable() method.
    }
}
```

### Console
Add ```DeleteTooOldIncompleteOrders``` to ```app/Console/Kernel.php```
```php
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(new DeleteLinksThatWereLastClickedTooLongAgo)->daily();
```

# Publishing
### Localization
```php
php artisan vendor:publish --tag="laravel-cashier-shop-lang"
```

### Views
```php
php artisan vendor:publish --tag="laravel-cashier-shop-views"
```

### Config
```php
php artisan vendor:publish --tag="laravel-cashier-shop-config"
```

### Migrations
```php
php artisan vendor:publish --tag="laravel-cashier-shop-migrations"
```