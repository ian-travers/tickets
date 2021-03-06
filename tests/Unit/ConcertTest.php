<?php


namespace Tests\Unit;

use App\Concert;
use App\ConcertFactory;
use App\Order;
use App\Exceptions\NotEnoughTicketsException;
use App\Ticket;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    /** @test */
    function can_get_formatted_date()
    {
        // Create a concert with a known date
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2019-03-10 8:00PM'),
        ]);

        // Verify the date is formatted as expected
        $this->assertEquals('March 10, 2019', $concert->formatted_date);
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2019-03-10 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->state('published')->create();
        $publishedConcertB = factory(Concert::class)->state('published')->create();
        $unpublishedConcert = factory(Concert::class)->state('unpublished')->create();

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function concert_can_be_published()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create([
            'published_at' => null,
            'ticket_quantity' => 5,
        ]);

        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $concert->publish();

        $this->assertTrue($concert->isPublished());
        $this->assertEquals(5, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 2)->create(['order_id' => null]));

        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_sold_only_includes_tickets_associated_with_an_order()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 2)->create(['order_id' => null]));

        $this->assertEquals(3, $concert->ticketsSold());
    }

    /** @test */
    function total_tickets_includes_all_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 2)->create(['order_id' => null]));

        $this->assertEquals(5, $concert->totalTickets());
    }

    /** @test */
    function calculating_the_revenue_in_dollars()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        /** @var Order $orderA */
        $orderA = factory(Order::class)->create(['amount' => 3850]);

        /** @var Order $orderB */
        $orderB = factory(Order::class)->create(['amount' => 9625]);

        $concert->tickets()->saveMany(factory(Ticket::class, 2)->create(['order_id' => $orderA->id]));
        $concert->tickets()->saveMany(factory(Ticket::class, 5)->create(['order_id' => $orderB->id]));

        $this->assertEquals(134.75, $concert->revenueInDollars());
    }

    /** @test */
    function calculating_percentage_of_tickets_sold()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 2)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 5)->create(['order_id' => null]));

        $this->assertEquals(28.57, $concert->percentSoldOut());
    }

    /** @test */
    function trying_to_reserve_more_tickets_than_remain_throws_an_exception()
    {
        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 10]);

        try {
            $reservation = $concert->reserveTickets(11, 'jane@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Заказ создан успешно, хотя свободных билетов для этого заказа не достаточно.");

    }

    /** @test */
    function can_reserve_available_tickets()
    {
        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
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

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $concert->reserveTickets(2, 'jane@example.com');

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Резервирование билетов успешно, хотя билеты уже зарезервированы");
    }
}