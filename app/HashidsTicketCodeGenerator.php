<?php


namespace App;

use Hashids\Hashids;

class HashidsTicketCodeGenerator implements TicketCodeGeneratorInterface
{
    private $hashids;

    public function __construct($salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public function generateFor($ticket)
    {
        /** @var Ticket $ticket */
        return $this->hashids->encode($ticket->id);
    }
}