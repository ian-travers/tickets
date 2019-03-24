<?php

namespace Tests\Unit;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 * Class StripePaymentGatewayTest
 *
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    private function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }

    /**
     *  WARNING: This test can't run without internet access
     *
     *  Use "phpunit --exclude-group integration" to run tests without this one
     */
    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new StripePaymentGateway
        $paymentGateway = $this->getPaymentGateway();

        // Create a new charge for some amount using a valid token
        $newCharges = $paymentGateway->newChargesDuring(function (StripePaymentGateway $paymentGateway) {
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
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        $paymentGateway->charge(2500, 'invalid-payment-token');

        $this->fail("Charging with an invalid payment token did't throw a PaymentFailedException");
    }
}
