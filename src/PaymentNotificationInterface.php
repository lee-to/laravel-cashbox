<?php

namespace App\CashBox;

interface PaymentNotificationInterface
{
    public function send(string $message);
}