<?php


namespace Leeto\CashBox;


/**
 * @method setPaymentDescription(string $string)
 * @method setAmount(float $float)
 */
interface PaymentGatewayInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    public function credentials(array $params = []);

    /**
     * @return mixed
     */
    public function getRequest();

    /**
     * @return array
     */
    public function getReceiptItems() : array;

    /**
     * @return array
     */
    public function getPaymentData() : array;

    /**
     * @return mixed
     */
    public function getPaymentObject();

    /**
     * @return string
     */
    public function createPayment() : string;

    /**
     * @param callable $callback
     * @return array
     */
    public function capturePayment(callable $callback) : array;

    /**
     * @return mixed
     */
    public function cancelPayment();

    /**
     * @return mixed
     */
    public function createRefund();
}