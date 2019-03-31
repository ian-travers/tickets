<?php

namespace Tests\Unit;

use App\HashidsTicketCodeGenerator;
use App\Ticket;
use App\TicketCodeGeneratorInterface;
use Tests\TestCase;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    public function test_ticket_code_at_least_6_characters_long()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator();

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    public function test_ticket_code_can_only_contain_uppercase_letters()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator();

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-Z]+$/', $code);
    }

    public function test_ticket_code_the_same_ticket_id_are_the_same()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator();

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }
}