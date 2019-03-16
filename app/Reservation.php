<?php

namespace App;
use Illuminate\Support\Collection;

/**
 * Class Reservation
 *
 * @property Collection $tickets
 *
 * @package App
 */
class Reservation
{

    private $tickets;

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }
}