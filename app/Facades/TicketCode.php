<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\TicketCodeGeneratorInterface;

class TicketCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TicketCodeGeneratorInterface::class;
    }

    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}