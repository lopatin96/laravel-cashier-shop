# Install
### Trait
Add ```HasOrders``` trait to User model.

```php
use Atin\LaravelCashierShop\Traits\HasOrders;

class User extends Authenticatable
{
    use HasOrders;
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