<?php

namespace Leeto\CashBox\PaymentGateways;

use Leeto\CashBox\Exceptions\PaymentNotificationException;
use Leeto\CashBox\PaymentGatewayInterface;

use YooKassa\Client;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

/**
 * Class YooKassa
 * @package Leeto\CashBox\PaymentGateways
 */
class YooKassa extends PaymentGateway implements PaymentGatewayInterface
{
    /**
     * YooKassa constructor.
     */
    public function __construct()
    {
        $this->setClient(new Client());
    }

    /**
     * @param array $params
     */
    public function credentials($params = []) {
        $this->getClient()->setAuth($params["id"], $params["key"]);
    }

    /**
     * @return mixed
     */
    public function getRequest() {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * @return array
     */
    public function getReceiptItems(): array
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

    /**
     * @return array
     */
    public function getPaymentData(): array
    {
        $data = [
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
            "metadata" => $this->getParams(),
            "save_payment_method" => true,
        ];

        if($this->isRecurringPayments() && $this->getPaymentToken()) {
            $data["payment_method_id"] = $this->getPaymentToken();
        }

        if($this->isSaveBankCard()) {
            $data["save_payment_method"] = true;
            $data["payment_method_data"]["type"] = "bank_card";
        }

        return $data;
    }

    /**
     * @return \YooKassa\Model\Payment|\YooKassa\Model\PaymentInterface|\YooKassa\Request\Payments\PaymentResponse
     * @throws PaymentNotificationException
     */
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

    /**
     * @return string
     */
    public function createPayment() : string
    {
        $this->logger("create", $this->getPaymentData());

        $this->tryNotify();

        $response = $this->getClient()->createPayment($this->getPaymentData(), $this->getIdempotent());

        return $response->getConfirmation()->getConfirmationUrl();
    }

    /**
     * @param callable $callback
     * @return array
     * @throws PaymentNotificationException
     */
    public function capturePayment(callable $callback) : array
    {
        $payment = $this->getPaymentObject();

        if ($payment->getPaid()) {
            $params = $payment->getMetadata()->toArray();

            if ($payment->getPaymentMethod()->getSaved()) {
                $this->setSavedBankCard($payment->getPaymentMethod()->jsonSerialize());
                $this->setSaveBankCard(true);
                $this->setPaymentToken($payment->getPaymentMethod()->getId());
            }

            $amount = $this->amountFormat($payment->getAmount()->getValue());

            $this->setAmount($amount);
            $this->setPaymentDescription($payment->getDescription());
            $this->setParams($params);

            $this->captureCallable($callback, $params);

            return $this->getClient()->capturePayment($this->getPaymentData(), $payment->getId(), $this->getIdempotent());
        }

        return [];
    }

    /**
     *
     */
    public function cancelPayment()
    {

    }

    /**
     *
     */
    public function createRefund()
    {

    }
}