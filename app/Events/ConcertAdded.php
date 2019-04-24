<?php

namespace App\Events;

use App\Concert;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ConcertAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $concert;

    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }
}
