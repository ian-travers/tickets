<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\InvitationCodeGeneratorInterface;

class InvitationCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return InvitationCodeGeneratorInterface::class;
    }
}