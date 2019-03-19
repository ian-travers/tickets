<?php

namespace Tests\Unit;

use App\Concert;
use App\Reservation;
use App\Ticket;
use Mockery\Mock;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    public function test_calculating_the_total_cost()
    {
        /** @var Concert $concert */
//        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);
//        $tickets = $concert->findTickets(5);

        $tickets = collect([
            (object)['price' => 1200],
            (object)['price' => 1200],
            (object)['price' => 1200],
            (object)['price' => 1200],
            (object)['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(6000, $reservation->totalCost());
    }

    public function test_reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = collect([
            \Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
            \Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
            \Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
        ]);

        $reservation = new Reservation($tickets);

        $reservation->cancel();
    }
}