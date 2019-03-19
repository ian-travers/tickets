<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Tests\TestCase;

class TicketTest extends TestCase
{
    public function test_a_ticket_can_be_reserved()
    {
        $ticket = factory(Ticket::class)->create();

        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    public function test_a_ticket_can_be_released()
    {
        /** @var Ticket $ticket */
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);


    }
}