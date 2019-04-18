<?php

use App\Concert;
use App\ConcertFactory;
use App\OrderFactory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $user = factory(App\User::class)->create([
            'email' => 'adam@example.com',
            'password' => bcrypt('secret'),
        ]);

        ConcertFactory::createPublished([
            'user_id' => $user->id,
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'additional_info' => 'This concert is 19+.',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'date' => Carbon::parse('2019-05-14 8:00pm'),
            'ticket_price' => 3250,
            'ticket_quantity' => 10
        ]);

        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
            'title' => 'Slayer',
            'subtitle' => 'with Forbidden and Testament',
            'additional_info' => null,
            'venue' => 'The Rock Pile',
            'venue_address' => '55 Sample Blvd',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '19276',
            'date' => Carbon::parse('2019-04-16 7:00pm'),
            'ticket_price' => 5500,
            'ticket_quantity' => 13
        ]);

        OrderFactory::createForConcert($concert,[
            'created_at' => Carbon::parse('2019-04-14 9:36pm'),
            'amount' => $concert->ticket_price * 3,

        ], 3);
        OrderFactory::createForConcert($concert,[
            'created_at' => Carbon::parse('2019-04-14 10:11pm'),
            'amount' => $concert->ticket_price * 2,
        ], 2);

        factory(Concert::class)->create(['user_id' => $user->id]);
    }
}
