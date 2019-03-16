<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_create_order_from_tickets_and_email()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        /** @var Order $order */
        $order = Order::forTickets($concert->findTickets(3), 'john@example.com');

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    public function test_converting_to_an_array()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(10);

        /** @var Order $order */
        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();
        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000
        ], $result);
    }

    public function test_tickets_are_released_when_an_order_is_cancelled()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(10);

        /** @var Order $order */
        $order = $concert->orderTickets('jane@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}