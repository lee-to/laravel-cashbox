<?php

namespace Leeto\CashBox;

/**
 * Interface PaymentNotificationInterface
 * @package Leeto\CashBox
 */
interface PaymentNotificationInterface
{
    /**
     * @param string $message
     * @return bool
     */
    public function send(string $message): bool;
}