<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ticket
 *
 * @property int $id
 * @property int $order_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
        return $query->whereNull('order_id');
    }
}
