<?php

use Faker\Generator as Faker;
use App\Concert;
use Carbon\Carbon;

$factory->define(Concert::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
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

$factory->state(Concert::class, 'published', function (Faker $faker) {
    return [
        'published_at' => Carbon::parse('-1 weeks'),
    ];
});

$factory->state(Concert::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null,
    ];
});