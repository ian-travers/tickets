<?php

namespace App\Billing;

use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGatewayInterface
{
    const TEST_CARD_NUMBER = '4242424242424242';

    private $charges;
    private $tokens;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'fake_tok_' . Str::random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException();
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
        ]);
    }

    public function newChargesDuring($callback)
    {
        /**
         *  Defining the key value of the charges array when new charges from callback will be added
         *  Solution: store the the array count before (cos zero-based array)
         *  [
         *      0 => 2000,
         *      1 => 3000,
         *      // run callback
         *      2 => 4000,
         *      3 => 5000,
         *  ]
         *
         */

        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}