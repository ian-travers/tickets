<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @property int $id
 * @property int $concert_id
 * @property int $amount
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App
 */
class Order extends Model
{
    protected $guarded = [];

    public static function forTickets($tickets, $email): self
    {
        /** @var Order $order */
        $order = self::create([
            'email' => $email,
            'amount' => $tickets->sum('price'),
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    public function cancel()
    {
        /** @var Ticket $ticket */
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }

        $this->delete();
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }
}
