# Install
### Trait
Add ```HasOrders``` trait to User model.

```php
use Atin\LaravelCashierShop\Traits\HasOrders;

class User extends Authenticatable
{
    use HasOrders;
```