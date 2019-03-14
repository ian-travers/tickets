<?php

namespace App;

use Carbon\Carbon;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class Concert
 *
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property Carbon $date
 * @property int $ticket_price
 * @property string $venue
 * @property string $venue_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_info
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read  string $formatted_date
 * @property-read  string $formatted_start_time
 *
 * @method static Builder published()
 *
 * @package App
 */
class Concert extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100 , 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity): Order
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        /* @var Order $order */
        $order = $this->orders()->create(['email' => $email]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }
}
