# laravel-cashbox beta

## Install
- composer require lee-to/laravel-cashbox

- php artisan vendor:publish --provider="Leeto\CashBox\Providers\CashBoxServiceProvider"

- php artisan cashbox:install

- configure config/cashbox.php

### Available payment gateways
- YooKassa (config/cashbox.php credentials - id(shopId), key(shopPassword))
- KassaCom (config/cashbox.php credentials - login, secret, key(api-key))

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
return response()->json(app("payment")->capturePayment(function ($paymentParams, $paymentToken, $bankCard) {
    
}));
```

##### Tests
- vendor/bin/phpunit tests
