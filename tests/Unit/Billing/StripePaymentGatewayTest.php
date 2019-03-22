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
    protected function setUp(): void
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    /**
     *  WARNING: This test can't run without internet access
     *
     *  Use "phpunit --exclude-group integration" to run tests without this one
     */
    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        // Create a new StripePaymentGateway
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        // Create a new charge for some amount using a valid token
        $paymentGateway->charge(2500, $this->validToken());

        // Verify that the charge was completed successfully
        $this->assertCount(1, $this->newCharges($this->lastCharge));
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }

    private function lastCharge()
    {
        return \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
    }

    private function newCharges()
    {
        return \Stripe\Charge::all(
            [
                'limit' => 1,
                'ending_before' => $this->lastCharge->id,
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }

    private function validToken()
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ]
        ], ['api_key' => config('services.stripe.secret')])->id;
    }

}
