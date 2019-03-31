<?php

namespace App;

use App\Billing\PaymentGatewayInterface;
use Illuminate\Support\Collection;

/**
 * Class Reservation
 *
 * @package App
 */
class Reservation
{

    private $tickets;
    private $email;

    public function __construct(Collection $tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }

    public function complete($paymentGateway, $paymentToken): Order
    {
        /** @var PaymentGatewayInterface $paymentGateway */
        $charge = $paymentGateway->charge($this->totalCost(), $paymentToken);
        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}