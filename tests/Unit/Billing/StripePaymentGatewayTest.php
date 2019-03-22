<?php

namespace Tests\Unit;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    private function lastCharge()
    {
        return \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
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

    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        $lastCharge = $this->lastCharge();

        // Create a new StripePaymentGateway
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        // Create a new charge for some amount using a valid token
        $paymentGateway->charge(2500, $this->validToken());

        // Verify that the charge was completed successfully
        $newCharge = \Stripe\Charge::all(
            [
                'limit' => 1,
                'ending_before' => $lastCharge->id,
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];

        $this->assertEquals(2500, $newCharge->amount);
    }
}
