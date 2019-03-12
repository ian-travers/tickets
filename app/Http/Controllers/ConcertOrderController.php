<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGatewayInterface;
use App\Concert;
use App\Order;

class ConcertOrderController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::find($concertId);

        $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));

        /* @var Order $order */
        $order = $concert->orders()->create(['email' => request('email')]);

        foreach (range(1, request('ticket_quantity')) as $i) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
