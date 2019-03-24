<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());

    }

    /**
     * @expectedException \App\Billing\PaymentFailedException
     */
    public function test_charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2500, 'invalid-payment-token');

        $this->fail("Charging with an invalid payment token did't throw a PaymentFailedException");
    }

    public function test_running_a_hook_before_the_first_charge()
    {
        /** @var FakePaymentGateway $paymentGateway */
        $paymentGateway = new FakePaymentGateway();

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
