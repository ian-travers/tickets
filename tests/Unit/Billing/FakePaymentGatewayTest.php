<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway();
    }

    /**
     * @expectedException \App\Billing\PaymentFailedException
     */
    public function test_charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2500, 'invalid-payment-token');

        $this->fail("Charging with an invalid payment token did't throw a PaymentFailedException");
    }

    public function test_running_a_hook_before_the_first_charge()
    {
        $paymentGateway = $this->getPaymentGateway();

        $timesCallbackRan = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            /** @var FakePaymentGateway $paymentGateway */
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }
}
