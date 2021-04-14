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
     * @return mixed
     */
    public function send(string $message);
}