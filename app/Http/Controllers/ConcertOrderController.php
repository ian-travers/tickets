<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGatewayInterface;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;

class ConcertOrderController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        /** @var Concert $concert */
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => 'required|email',
            'ticket_quantity' => 'required|integer|min:1',
            'payment_token' => 'required',
        ]);

        try {
            // Find some tickets
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            // Charge the customer for the tickets
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            // Create order for those tickets
            $order = $reservation->complete();


            return response()->json($order, 201);

        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }


    }
}
