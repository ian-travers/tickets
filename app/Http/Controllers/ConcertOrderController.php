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
        $this->validate(request(), [
            'email' => 'required',
        ]);

        $concert = Concert::find($concertId);
        $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));

        /* @var Order $order */
        $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

        return response()->json([], 201);
    }
}
