<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\OrderConfirmationNumberGeneratorInterface;

class OrderConfirmationNumber extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGeneratorInterface::class;
    }
}