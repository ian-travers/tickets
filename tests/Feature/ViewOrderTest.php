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
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        /** @var Ticket $ticket */
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
        ]);

        $response = $this->get("/orders/ORDERCONFIRMATION1234");
        $response->assertStatus(200);
    }
}
