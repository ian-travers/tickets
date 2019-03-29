<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    function test_user_can_view_their_order_confirmation()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        /** @var Order $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '7072',
            'amount' => 7500,
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
        $response->assertSee('$75.00');
        $response->assertSee('**** **** **** 7072');
        $response->assertSee('TICKETCODE1234');
        $response->assertSee('TICKETCODE5678');
    }
}
