<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    protected function getPaymentGateway()
    {
        return new FakePaymentGateway();
    }

    public function test_can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([4000, 5000], $newCharges->all());
    }

    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new FakePaymentGateway
        $paymentGateway = $this->getPaymentGateway();

        // Create a new charge for some amount using a valid token
        $newCharges = $paymentGateway->newChargesDuring(function (FakePaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // Verify that the charge was completed successfully
        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
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
