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
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    public function test_ticket_code_can_only_contain_uppercase_letters()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-Z]+$/', $code);
    }

    public function test_ticket_code_the_same_ticket_id_are_the_same()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    public function test_ticket_code_for_different_ticket_id_are_different()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator */
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    public function test_ticket_codes_generated_with_different_salts_are_different()
    {
        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator1 */
        $ticketCodeGenerator1 = new HashidsTicketCodeGenerator('testsalt1');

        /** @var TicketCodeGeneratorInterface $ticketCodeGenerator2 */
        $ticketCodeGenerator2 = new HashidsTicketCodeGenerator('testsalt2');

        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}