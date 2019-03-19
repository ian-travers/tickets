<?php

use Faker\Generator as Faker;
use App\Ticket;
use Carbon\Carbon;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(\App\Concert::class)->create()->id;
        },

    ];
});

$factory->state(Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
