<?php

namespace Leeto\CashBox\PaymentGateways;

use KassaCom\SDK\Model\NotificationTypes;
use KassaCom\SDK\Model\PaymentStatuses;
use KassaCom\SDK\Notification;
use Leeto\CashBox\PaymentGatewayInterface;

use KassaCom\SDK\Client;

/**
 * Class KassaCom
 * @package Leeto\CashBox\PaymentGateways
 */
class KassaCom extends PaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var
     */
    protected $notification;

    /**
     * KassaCom constructor.
     */
    public function __construct()
    {
        $this->setClient(new Client());
        $this->setNotification(new Notification());
    }

    /**
     * @return Notification
     */
    protected function getNotification() : Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     */
    protected function setNotification($notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @param array $params
     */
    public function credentials($params = []) {
        $this->getClient()->setAuth($params["login"], $params["secret"]);
        $this->getNotification()->setApiKey($params["key"]);
    }

    /**
     * @return \KassaCom\SDK\Model\Request\NotificationRequest
     * @throws \KassaCom\SDK\Exception\Notification\EmptyApiKeyException
     * @throws \KassaCom\SDK\Exception\Notification\NotificationSecurityException
     */
    public function getRequest(): \KassaCom\SDK\Model\Request\NotificationRequest
    {
        return $this->getNotification()->process(false);
    }

    /**
     * @return array
     */
    public function getReceiptItems(): array
    {
        $receiptItems = parent::getReceiptItems();

        if(empty($receiptItems)) {
            $receiptItems[] = [
                "name" => $this->getPaymentDescription(),
                "price" => $this->getAmount(),
                "sum" => $this->getAmount(),
                "quantity" => 1,
                "tax" => config("cashbox.gateway.vat_code") ?? "none"
            ];
        }

        return $receiptItems;
    }

    /**
     * @return array
     */
    public function getPaymentData(): array
    {
        $params = $this->getParams();

        if(!isset($params["email"])) {
            throw new \InvalidArgumentException("Customer Email is required");
        }

        $data = [
            'order' => [
                'amount' => $this->getAmount(),
                'currency' => config("cashbox.gateway.currency") ?? 'RUB',
                'description' => $this->getPaymentDescription(),
            ],
            'settings' => [
                'project_id' => config("cashbox.gateway.project_id"),
                'success_url' => $this->getReturnUrl()
            ],
            'receipt' => [
                'items' => $this->getReceiptItems(),
                'email' => $params["email"],
                'place' => 'site',
            ],
            'custom_parameters' => $params
        ];

        if($this->isRecurringPayments() && $this->getPaymentToken()) {
            $data["settings"]["subscription_token"] = $this->getPaymentToken();
        }

        if($this->isSaveBankCard()) {
            $data["settings"]["create_subscription"] = true;
        }

        return $data;
    }

    /**
     * @return \KassaCom\SDK\Model\Request\NotificationRequest
     * @throws \KassaCom\SDK\Exception\Notification\EmptyApiKeyException
     * @throws \KassaCom\SDK\Exception\Notification\NotificationSecurityException
     */
    public function getPaymentObject(): \KassaCom\SDK\Model\Request\NotificationRequest
    {
        $request = $this->getRequest();

        $this->logger($request->getStatus(), (array) $request);

        return $request;
    }

    /**
     * @return string
     */
    public function createPayment() : string
    {
        $this->logger("create", $this->getPaymentData());

        $this->notify(config("cashbox.notify.try_payment_message") . $this->getDefaultNotifyMessage());

        $response = $this->getClient()->createPayment($this->getPaymentData());

        return $response->getPaymentUrl();
    }

    /**
     * @param callable $callback
     * @return string[]
     * @throws \KassaCom\SDK\Exception\Notification\EmptyApiKeyException
     * @throws \KassaCom\SDK\Exception\Notification\NotificationSecurityException
     */
    public function capturePayment(callable $callback) : array
    {
        try {
            $payment = $this->getPaymentObject();

            if($payment->getNotificationType() == NotificationTypes::TYPE_PAY && $payment->getStatus() == PaymentStatuses::STATUS_SUCCESSFUL) {

                if ($payment->getToken() && $payment->getPaymentMethod()->getCard()) {
                    $this->setSavedBankCard((array) $payment->getPaymentMethod()->getCard());
                    $this->setSaveBankCard(true);
                    $this->setPaymentToken($payment->getToken());
                }

                $this->captureCallable($callback, $payment->getCustomParameters());
            }
        } catch (\Exception $e) {
            return [
                "status" => "error",
                "message" => $e->getMessage(),
            ];
        }

        return [
            "status" => "ok",
            "message" => "",
        ];
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