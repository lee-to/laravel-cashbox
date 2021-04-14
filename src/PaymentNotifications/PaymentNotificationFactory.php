<?php


namespace Leeto\CashBox\PaymentNotifications;

use Leeto\CashBox\PaymentNotificationInterface;

/**
 * Class PaymentNotificationFactory
 * @package Leeto\CashBox\PaymentNotifications
 */
class PaymentNotificationFactory
{
    /**
     * PaymentNotificationFactory constructor.
     */
    private function __construct() {

    }

    /**
     * @param $handler
     * @return PaymentNotificationInterface|Telegram
     */
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