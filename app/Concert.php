<?php

namespace App;

use Carbon\Carbon;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

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
 * @property-read string $formatted_date
 * @property-read string $formatted_start_time
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
        return $this->belongsToMany(Order::class, 'tickets');
//        return $this->hasMany(Order::class);
    }

    public function hasOrderFor($customerEmail): bool
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($email, $ticketQuantity): Order
    {
        $tickets = $this->findTickets($ticketQuantity);
        return $this->createOrder($email, $tickets);
    }

    public function reserveTickets($quantity, $email): Reservation
    {
        $tickets =  $this->findTickets($quantity)->each(function (Ticket $ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();
        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }

        return $tickets;
    }

    public function createOrder($email, $tickets): Order
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
    }

    public function addTickets($quantity): self
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }
}
