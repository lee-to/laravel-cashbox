<?php


namespace App\CashBox\PaymentNotifications;

use App\CashBox\PaymentNotificationInterface;

class PaymentNotificationFactory
{
    private function __construct() {

    }

    public static function make($handler)
    {
        if ($handler instanceof PaymentNotificationInterface) {
            return $handler;
        }

        if ('telegram' === $handler) {
            return new Telegram();
        }

        throw new \InvalidArgumentException('The cashbox notification handler must be set to "telegram" or an instance of PaymentNotificationInterface');
    }
}