<?php

use Faker\Generator as Faker;
use App\Concert;
use Carbon\Carbon;

$factory->define(Concert::class, function (Faker $faker) {
    return [
        'title' => 'Example Band',
        'subtitle' => 'with the Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000, // in cents
        'venue' => 'The Example Hall',
        'venue_address' => '123 Example line',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '99999',
        'additional_info' => 'Some sample additional information',
    ];
});
