<?php

namespace App;

use App\Facades\TicketCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ticket
 *
 * @property int $id
 * @property int $order_id
 * @property Carbon|null $reserved_at
 * @property string|null $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Concert $concert
 *
 * @method static Builder available()
 *
 * @package App
 */
class Ticket extends Model
{
    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    public function claimFor(Order $order)
    {
        $this->code = TicketCode::generate();
        $order->tickets()->save($this);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
