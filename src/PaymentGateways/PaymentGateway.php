<?php


namespace App\CashBox\PaymentGateways;

use App\Models\CashBoxRequest;
use App\CashBox\PaymentNotifications\PaymentNotificationFactory;

class PaymentGateway
{
    protected $client;

    protected $amount;

    protected $returnUrl;

    protected $paymentDescription;

    protected $params = [];

    protected $receiptItems = [];

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

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getPaymentDescription(): string
    {
        return $this->paymentDescription;
    }

    /**
     * @param string $paymentDescription
     */
    public function setPaymentDescription(string $paymentDescription): void
    {
        $this->paymentDescription = $paymentDescription;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    protected function getReceiptItems() : array {
        return $this->receiptItems;
    }

    public function addReceiptItem(array $item) {
        $this->receiptItems[] = $item;
    }

    protected function getIdempotent(): string
    {
        return sha1("idemp" . uniqid("", true));
    }

    protected function amountFormat($value): string
    {
        return number_format($value, 2, ".", "");
    }

    /**
     * @return string
     */
    protected function getDefaultNotifyMessage(): string
    {
        return "{$this->getPaymentDescription()} - {$this->getAmount()} " . config("cashbox.gateway.currency");
    }

    protected function logger(string $event, array $params) {
        return CashBoxRequest::create([
            "request_event_type" => $event,
            "request_data" => $params
        ]);
    }

    protected function notify(string $message) {
        return PaymentNotificationFactory::make(config("cashbox.notify.default"))->send($message);
    }
}