<?php

use Faker\Generator as Faker;
use App\Order;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'amount' => 5250,
        'email' => $faker->email,
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'card_last_four' => $faker->numerify('####'),
    ];
});

