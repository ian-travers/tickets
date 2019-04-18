<?php


namespace App;


class OrderFactory
{
    public static function createForConcert(Concert $concert, $overrides = [], $ticketQuantity = 1): Order
    {
        /** @var Order $order */
        $order = factory(Order::class)->create($overrides);
        $tickets = factory(Ticket::class, $ticketQuantity)->create(['concert_id' => $concert->id]);
        $order->tickets()->saveMany($tickets);

        return $order;
    }
}