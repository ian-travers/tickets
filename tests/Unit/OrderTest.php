<?php

namespace Tests\Unit;

use App\Billing\Charge;
use App\Order;
use App\Ticket;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_create_order_from_tickets_email_and_charge()
    {
        $tickets = factory(Ticket::class, 3)->create();
        $charge = new Charge(['amount' => 3600, 'card_last_four' => '1234']);

        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four);
    }

    public function test_retrieving_an_order_by_confirmation_number()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION123',
        ]);

        /** @var Order $foundOrder */
        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION123');

        $this->assertEquals($order->id, $foundOrder->id);
        $this->assertEquals($order->confirmation_number, $foundOrder->confirmation_number);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_retrieving_a_nonexistent_order_by_confirmation_number_throws_an_exception()
    {
        Order::findByConfirmationNumber('NONEXISTINGCONFIRMATIONNUMBER');

        $this->fail('No matching order was found for the specified confirmation number, but an exception was not thrown');
    }

    public function test_converting_to_an_array()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000,
        ]);

        $order->tickets()->saveMany([
            factory(Ticket::class)->create(['code' => 'TICKETCODE1']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE2']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE3']),
        ]);

        $result = $order->toArray();
        $this->assertEquals([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'jane@example.com',
            'amount' => 6000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ],
        ], $result);
    }

}