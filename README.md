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
use Atin\LaravelCashierShop\Models\Order;

class TestProduct extends Product
{
    protected function run(Order $order): void
    {
         $order->user->config->...
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