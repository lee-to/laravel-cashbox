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
     */
    public function __construct(PaymentGatewayInterface $paymentGateway = null)
    {
        if(is_null($paymentGateway)) {
            $paymentGatewayClass = config("cashbox.gateway.class");
            $this->paymentGateway = new $paymentGatewayClass();
            $this->paymentGateway->credentials(config("cashbox.gateway.credentials"));
        }
    }

    /**
     * @return PaymentGatewayInterface
     */
    public function payment() : PaymentGatewayInterface {
        return $this->paymentGateway;
    }
}