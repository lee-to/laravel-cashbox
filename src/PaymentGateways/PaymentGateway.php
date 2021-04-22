<?php


namespace Leeto\CashBox\PaymentGateways;

use Leeto\CashBox\Models\CashBoxRequest;
use Leeto\CashBox\PaymentNotifications\PaymentNotificationFactory;

/**
 * Class PaymentGateway
 * @package Leeto\CashBox\PaymentGateways
 */
class PaymentGateway
{
    /**
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $paymentToken;

    /**
     * @var
     */
    protected $amount;

    /**
     * @var
     */
    protected $returnUrl;

    /**
     * @var string
     */
    protected $paymentDescription = "";

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $receiptItems = [];


    /**
     * @var array
     */
    protected $savedBankCard = [];


    /**
     * @var bool
     */
    protected $recurringPayments = false;

    /**
     * @var bool
     */
    protected $saveBankCard = false;


    protected $notifyCustomText = "";

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

    /**
     * @return array
     */
    protected function getReceiptItems() : array {
        return $this->receiptItems;
    }

    /**
     * @param array $item
     */
    public function addReceiptItem(array $item) {
        $this->receiptItems[] = $item;
    }

    /**
     * @return mixed
     */
    public function getPaymentToken()
    {
        return $this->paymentToken;
    }

    /**
     * @param mixed $paymentToken
     */
    public function setPaymentToken($paymentToken): void
    {
        $this->paymentToken = $paymentToken;
    }


    /**
     * @return bool
     */
    public function isRecurringPayments(): bool
    {
        return $this->recurringPayments;
    }

    /**
     * @param bool $recurringPayments
     */
    public function setRecurringPayments(bool $recurringPayments): void
    {
        $this->recurringPayments = $recurringPayments;
    }

    /**
     * @return bool
     */
    public function isSaveBankCard(): bool
    {
        return $this->saveBankCard;
    }

    /**
     * @param bool $saveBankCard
     */
    public function setSaveBankCard(bool $saveBankCard): void
    {
        $this->saveBankCard = $saveBankCard;
    }

    /**
     * @return array
     */
    public function getSavedBankCard(): array
    {
        return $this->savedBankCard;
    }

    /**
     * @param array $savedBankCard
     */
    public function setSavedBankCard(array $savedBankCard): void
    {
        $this->savedBankCard = $savedBankCard;
    }

    /**
     * @return string
     */
    public function getNotifyCustomText(): string
    {
        return $this->notifyCustomText;
    }

    /**
     * @param string $notifyCustomText
     */
    public function setNotifyCustomText(string $notifyCustomText): void
    {
        $this->notifyCustomText = $notifyCustomText;
    }

    /**
     * @return string
     */
    protected function getIdempotent(): string
    {
        return sha1("idemp" . uniqid("", true));
    }

    /**
     * @param $value
     * @return string
     */
    protected function amountFormat($value): string
    {
        return number_format($value, 2, ".", "");
    }

    /**
     * @return string
     */
    protected function getDefaultNotifyMessage(): string
    {
        return trim("{$this->getNotifyCustomText()} {$this->getPaymentDescription()} " . config("cashbox.gateway.currency"));
    }

    /**
     * @param $callback
     * @param array $params
     */
    protected function captureCallable($callback, array $params) {
        if(is_callable($callback)) {
            $callback($params, $this->getPaymentToken(), $this->getSavedBankCard());
        }
    }

    /**
     *
     */
    public function tryNotify() {
        if(config("cashbox.notify.try_payment_message")) {
            $this->notify(config("cashbox.notify.try_payment_message") . $this->getDefaultNotifyMessage());
        }
    }
    
    /**
     *
     */
    public function captureNotify() {
        if(config("cashbox.notify.new_payment_message")) {
            $this->notify(config("cashbox.notify.new_payment_message") . $this->getDefaultNotifyMessage());
        }
    }

    /**
     * @param string $event
     * @param array $params
     * @return mixed
     */
    protected function logger(string $event, array $params) {
        return CashBoxRequest::create([
            "request_event_type" => $event,
            "request_data" => $params
        ]);
    }

    /**
     * @param string $message
     * @return false
     */
    protected function notify(string $message) {
        return PaymentNotificationFactory::make(config("cashbox.notify.default"))->send($message);
    }
}