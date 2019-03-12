<?php

namespace App\Billing;

interface PaymentGatewayInterface
{
    public function charge($amount, $token);
}