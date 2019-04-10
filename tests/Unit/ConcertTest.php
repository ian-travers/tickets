<?php


namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Exceptions\NotEnoughTicketsException;
use App\Ticket;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    public function test_can_get_formatted_date()
    {
        // Create a concert with a known date
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2019-03-10 8:00PM'),
        ]);

        // Verify the date is formatted as expected
        $this->assertEquals('March 10, 2019', $concert->formatted_date);
    }

    public function test_can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2019-03-10 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    public function test_can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    public function test_concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->state('published')->create();
        $publishedConcertB = factory(Concert::class)->state('published')->create();
        $unpublishedConcert = factory(Concert::class)->state('unpublished')->create();

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    public function test_concert_can_be_published()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create(['published_at' => null]);

        $this->assertFalse($concert->isPublished());

        $concert->publish();

        $this->assertTrue($concert->isPublished());
    }

    public function test_can_add_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    public function test_tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 30)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 20)->create(['order_id' => null]));

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    public function test_trying_to_reserve_more_tickets_than_remain_throws_an_exception()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $reservation = $concert->reserveTickets(11, 'jane@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Заказ создан успешно, хотя свободных билетов для этого заказа не достаточно.");

    }

    public function test_can_reserve_available_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(3);

        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    public function test_cannot_reserve_tickets_that_have_already_been_purchased()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(3);

        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->tickets()->saveMany($concert->tickets->take(2));

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Резервирование билетов успешно, хотя билеты уже проданы");
    }

    public function test_cannot_reserve_tickets_that_have_already_been_reserved()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(3);

        $concert->reserveTickets(2,'jane@example.com');

        try {
            $concert->reserveTickets(2,'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Резервирование билетов успешно, хотя билеты уже зарезервированы");
    }
}