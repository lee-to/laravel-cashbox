<?php

namespace Leeto\CashBox;


/**
 * Class CashBox
 * @package Leeto\CashBox
 */
class CashBox
{
    /**
     * @var PaymentGatewayInterface|mixed
     */
    protected $paymentGateway;

    /**
     * CashBox constructor.
     * @param PaymentGatewayInterface|null $paymentGateway
     * @param boolean $isLogRequests
     */
    public function __construct(PaymentGatewayInterface $paymentGateway = null, bool $isLogRequests = true)
    {
        if(is_null($paymentGateway)) {
            $paymentGatewayClass = config("cashbox.gateway.class");
            $this->paymentGateway = new $paymentGatewayClass();
            $this->paymentGateway->setLogRequests($isLogRequests);
            $this->paymentGateway->credentials(config("cashbox.gateway.credentials"));
        }
    }

    /**
     * @return PaymentGatewayInterface
     */
    public function payment() : PaymentGatewayInterface
    {
        return $this->paymentGateway;
    }
}