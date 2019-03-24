<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGatewayInterface
{
    private $charges;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }

        $this->charges[] = $amount;
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
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}