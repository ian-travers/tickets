<?php


namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Exceptions\NotEnoughTicketsException;
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

    public function test_can_order_concert_tickets()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(3);

        /* @var Order $order */
        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
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
        $concert = factory(Concert::class)->create()->addTickets(50);

        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    public function test_trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Заказ создан успешно, хотя свободных билетов для этого заказа не достаточно.");

    }

    public function test_cannot_order_tickets_that_have_already_purchased()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create()->addTickets(10);

        $concert->orderTickets('jane@example.com', 8);
        try {
            $concert->orderTickets('jphn@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Заказ создан успешно, хотя свободных билетов для этого заказа не достаточно.");

    }
}