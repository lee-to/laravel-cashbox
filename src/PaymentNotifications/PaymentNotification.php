<?php


namespace Leeto\CashBox\PaymentNotifications;


/**
 * Class PaymentNotification
 * @package Leeto\CashBox\PaymentNotifications
 */
class PaymentNotification
{
    /**
     * @var
     */
    protected $client;

    /**
     * @return mixed
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    protected function setClient($client): void
    {
        $this->client = $client;
    }
}