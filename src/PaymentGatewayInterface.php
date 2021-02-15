<?php


namespace App\CashBox;


/**
 * @method setPaymentDescription(string $string)
 * @method setAmount(float $float)
 */
interface PaymentGatewayInterface
{
    public function credentials($params = []);

    public function createPayment();

    public function capturePayment(callable $callback);

    public function cancelPayment();

    public function createRefund();
}