<?php

use Faker\Generator as Faker;
use App\Order;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'amount' => 5250,
        'email' => 'somebody@example.com'
    ];
});

