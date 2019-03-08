<?php


namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    public function test_can_get_formatted_date()
    {
        // Create a concert with a known date
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2019-03-10 8:00PM'),
        ]);

        // Verify the date is formatted as expected
        $this->assertEquals('March 10, 2019', $date = $concert->formatted_date);
    }
}