<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    public function test_user_can_view_a_published_concert_listing()
    {
        // Arrange | Организация
        // Create a concert
        $concert = Concert::create([
            'title' => 'Industrial and Electronic',
            'subtitle' => 'with featuring Camouflage',
            'date' => Carbon::parse('March 10, 2019 8:00pm'),
            'ticket_price' => 3250, // in cents
            'venue' => 'Albert Hall',
            'venue_address' => '123 Exceed line',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17456',
            'additional_info' => 'For tickets, call (555) 555-5555',
            'published_at' => Carbon::parse('-1 week'),
        ]);

        // Act
        // View the concert listing
        $response = $this->get('/concerts/'. $concert->id);
        // Assert outcome obtained
        $response->assertStatus(200);


        // Assert
        // See the concert details
        $response->assertSee('Industrial and Electronic');
        $response->assertSee('with featuring Camouflage');
        $response->assertSee('March 10, 2019');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('Albert Hall');
        $response->assertSee('123 Exceed line');
        $response->assertSee('Laraville, ON 17456');
        $response->assertSee('For tickets, call (555) 555-5555');
    }

    public function test_user_cannot_view_unpublished_concert()
    {
        $concert = factory(Concert::class)->create([
            'published_at' => null,
        ]);

        $response = $this->get('/concerts/'. $concert->id);
        $response->assertStatus(404);
    }
}