<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    function test_user_can_view_their_order_confirmation()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('March 12, 2017 8:00pm'),
            'ticket_price' => 4250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
        ]);

        /** @var Order $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '7072',
            'amount' => 8500,
            'email' => 'john@example.com',
        ]);

        /** @var Ticket $ticketA */
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE1234',
        ]);

        /** @var Ticket $ticketB */
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE5678',
        ]);

        $response = $this->get("/orders/ORDERCONFIRMATION1234");
        $response->assertStatus(200);

        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $order->id === $viewOrder->id;
        });

        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('$85.00');
        $response->assertSee('**** **** **** 7072');
        $response->assertSee('TICKETCODE1234');
        $response->assertSee('TICKETCODE5678');
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON');
        $response->assertSee('17916');
        $response->assertSee('john@example.com');

        $response->assertSee('2017-03-12 20:00');
        $response->assertSee('Doors at 8:00pm');
    }
}
