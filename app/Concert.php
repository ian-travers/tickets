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
 * @property int $ticket_quantity
 * @property string $venue
 * @property string $venue_address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $additional_info
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read string $formatted_date
 * @property-read string $formatted_start_time
 *
 * @property User $user
 *
 * @method static Builder published()
 *
 * @package App
 */
class Concert extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);
        $this->addTickets($this->ticket_quantity);
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

    public function ticketsSold()
    {
        return $this->tickets()->sold()->count();
    }
}
