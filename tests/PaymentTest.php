<?php

namespace Leeto\CashBox\Tests;

use Leeto\CashBox\CashBox;
use Leeto\CashBox\PaymentGateways\KassaCom;
use Leeto\CashBox\PaymentGateways\YooKassa;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function test_yoo_kassa()
    {
        config([
            'cashbox.gateway.email' => 'test@test.com',
            'cashbox.gateway.class' => YooKassa::class,
            'cashbox.gateway.credentials.id' => 0,
            'cashbox.gateway.credentials.key' => 0,
        ]);

        $cashboxPayment = (new CashBox(null, false))->payment();
        $cashboxPayment->setPaymentDescription("Payment description");
        $cashboxPayment->setReturnUrl('/');
        $cashboxPayment->setAmount(100);
        $cashboxPayment->setParams([
            "meta_value" => "test",
        ]);

        $this->assertEquals("Payment description", $cashboxPayment->getPaymentDescription());
        $this->assertEquals("/", $cashboxPayment->getReturnUrl());
        $this->assertEquals(100, $cashboxPayment->getAmount());
        $this->assertArrayHasKey( "meta_value", $cashboxPayment->getParams());
    }

    public function test_kassa_com()
    {
        config([
            'cashbox.gateway.email' => 'test@test.com',
            'cashbox.gateway.class' => KassaCom::class,
            'cashbox.gateway.credentials.login' => 0,
            'cashbox.gateway.credentials.secret' => 0,
            'cashbox.gateway.credentials.key' => 0,
        ]);

        $cashboxPayment = (new CashBox(null, false))->payment();
        $cashboxPayment->setPaymentDescription("Payment description");
        $cashboxPayment->setReturnUrl('/');
        $cashboxPayment->setAmount(100);
        $cashboxPayment->setParams([
            "meta_value" => "test",
        ]);

        $this->assertEquals("Payment description", $cashboxPayment->getPaymentDescription());
        $this->assertEquals("/", $cashboxPayment->getReturnUrl());
        $this->assertEquals(100, $cashboxPayment->getAmount());
        $this->assertArrayHasKey( "meta_value", $cashboxPayment->getParams());
    }
}