<?php

namespace Tests\Unit\Billing;

trait PaymentGatewayContractTests
{
    abstract protected function getPaymentGateway();

    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new PaymentGateway
        $paymentGateway = $this->getPaymentGateway();

        // Create a new charge for some amount using a valid token
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // Verify that the charge was completed successfully
        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    public function test_can_get_details_about_a_successful_charge()
    {
        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(2500, $paymentGateway->getValidTestToken($paymentGateway::TEST_CARD_NUMBER));

        $this->assertEquals(substr($paymentGateway::TEST_CARD_NUMBER, -4), $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
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
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
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
}