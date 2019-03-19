<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_create_order_from_tickets_email_and_amount()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        /** @var Order $order */
        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

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
        $order = $concert->orderTickets('jane@example.com', 5, 6000);

        $result = $order->toArray();
        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000
        ], $result);
    }

}