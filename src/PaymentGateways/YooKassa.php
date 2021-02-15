<?php

namespace Leeto\CashBox\PaymentGateways;

use Leeto\CashBox\Exceptions\PaymentNotificationException;
use Leeto\CashBox\PaymentGatewayInterface;

use YooKassa\Client;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class YooKassa extends PaymentGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        $this->setClient(new Client());
    }

    public function credentials($params = []) {
        $this->getClient()->setAuth($params["id"], $params["key"]);
    }

    protected function getRequest() {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function getReceiptItems(): array
    {
        $receiptItems = parent::getReceiptItems();

        if(empty($receiptItems)) {
            $receiptItems[] = [
                "quantity" => 1,
                "amount" => [
                    "value" => $this->getAmount(),
                    "currency" => config("cashbox.gateway.currency") ?? 'RUB'
                ],
                "vat_code" => config("cashbox.gateway.vat_code") ?? 1,
                "description" => $this->getPaymentDescription(),
                'payment_subject' => config("cashbox.gateway.payment_subject") ?? 'intellectual_activity',
                'payment_mode' => config("cashbox.gateway.payment_mode") ?? 'full_payment',
            ];
        }

        return $receiptItems;
    }

    protected function getPaymentData(): array
    {
        return [
            'amount' => [
                'value' => $this->getAmount(),
                'currency' => config("cashbox.gateway.currency") ?? 'RUB'
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $this->getReturnUrl(),
            ],
            'description' => $this->getPaymentDescription(),
            'receipt' => [
                'items' => $this->getReceiptItems(),
                'tax_system_code' => config("cashbox.gateway.tax_system_code") ?? 1,
                'email' => config("cashbox.gateway.email"),
            ],
            "metadata" => $this->getParams()
        ];
    }

    public function getPaymentObject() {
        $request = $this->getRequest();

        if(is_null($request)) {
            throw new \InvalidArgumentException("Payment request is empty");
        }

        try {
            $this->logger($request['event'], $request);

            $notification = ($request['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($request)
                : new NotificationWaitingForCapture($request);
        } catch (\Exception $e) {
            throw new PaymentNotificationException($e->getMessage());
        }

        return $notification->getObject();
    }

    public function createPayment()
    {
        $this->logger("create", $this->getPaymentData());

        $this->notify(config("cashbox.notify.try_payment_message") . $this->getDefaultNotifyMessage());

        $response = $this->getClient()->createPayment($this->getPaymentData(), $this->getIdempotent());

        return $response->getConfirmation()->getConfirmationUrl();
    }

    public function capturePayment(callable $callback)
    {
        $payment = $this->getPaymentObject();

        if ($payment->getPaid()) {
            $params = $payment->getMetadata()->toArray();

            $amount = $this->amountFormat($payment->getAmount()->getValue());

            $this->setAmount($amount);
            $this->setPaymentDescription($payment->getDescription());
            $this->setParams($params);

            if(is_callable($callback)) {
                $callback();
            }

            $this->notify(config("cashbox.notify.new_payment_message") . $this->getDefaultNotifyMessage());

            return $this->getClient()->capturePayment($this->getPaymentData(), $payment->getId(), $this->getIdempotent());
        }

        return false;
    }

    public function cancelPayment()
    {

    }

    public function createRefund()
    {

    }
}