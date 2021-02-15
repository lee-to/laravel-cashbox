<?php

namespace Leeto\CashBox;

interface PaymentNotificationInterface
{
    public function send(string $message);
}