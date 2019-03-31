<?php


namespace App;


class HashidsTicketCodeGenerator implements TicketCodeGeneratorInterface
{
    public function generateFor($ticket)
    {
        return 'AAAAAA';
    }
}