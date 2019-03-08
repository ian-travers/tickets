<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
 *
 * @package App
 */
class Concert extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['date'];

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
}
