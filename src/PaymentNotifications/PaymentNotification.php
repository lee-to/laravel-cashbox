<?php


namespace App\CashBox\PaymentNotifications;


class PaymentNotification
{
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