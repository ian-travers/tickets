<?php

namespace App\Billing;


use Stripe\Charge;

class StripePaymentGateway implements PaymentGatewayInterface
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token)
    {
        Charge::create([
            'amount' => $amount,
            'source' => $token,
            'currency' => 'usd',
        ], ['api_key' => $this->apiKey]);
    }
}