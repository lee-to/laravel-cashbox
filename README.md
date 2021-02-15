# laravel-cashbox beta

## Install
- composer require lee-to/laravel-cashbox

- php artisan vendor:publish --provider="Leeto\CashBox\Providers\CashBoxServiceProvider"

- configure config/cashbox.php

### Available payment gateways
- YooKassa

### Usage

##### Create payment url

```php
app("payment")->setPaymentDescription("Premium");
app("payment")->setReturnUrl(route("home"));
app("payment")->setAmount(100);
app("payment")->setParams([
    "user_id" => auth()->id(),
]);

return redirect(app("payment")->createPayment());
```

##### Capture payment

```php
return response()->json(app("payment")->capturePayment(function () {
    
}));
```
