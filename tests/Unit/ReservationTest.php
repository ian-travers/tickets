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
        $ticket1 = \Mockery::mock(Ticket::class);
        $ticket1->shouldReceive('release')->once();

        $ticket2 = \Mockery::mock(Ticket::class);
        $ticket2->shouldReceive('release')->once();

        $ticket3 = \Mockery::mock(Ticket::class);
        $ticket3->shouldReceive('release')->once();

        $tickets = collect([$ticket1, $ticket2, $ticket3]);

        $reservation = new Reservation($tickets);

        $reservation->cancel();
    }
}