<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Ticket;
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
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(1);

        /** @var Order $order */
        $order = $concert->orderTickets('jane@example.com', 1);

        /** @var Ticket $ticket */
        $ticket = $order->tickets()->first();

        $this->assertNotNull($ticket);
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        $this->assertNull($ticket->fresh()->order_id);
    }
}