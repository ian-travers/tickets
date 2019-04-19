<?php

namespace Tests\Unit;

use App\Order;
use App\Ticket;
use App\Facades\TicketCode;
use Tests\TestCase;

class TicketTest extends TestCase
{
    /** @test */
    function a_ticket_can_be_reserved()
    {
        $ticket = factory(Ticket::class)->create();

        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function a_ticket_can_be_released()
    {
        /** @var Ticket $ticket */
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);


    }

    /** @test */
    function ticket_can_be_claimed_for_an_order()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();

        /** @var Ticket $ticket */
        $ticket = factory(Ticket::class)->create(['code' => null]);

        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');

        $ticket->claimFor($order);

        // Assert that the ticket is saved to the order
        $this->assertContains($ticket->id, $order->tickets->pluck('id'));

        // Arrest that the ticket had the expected code generated
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}