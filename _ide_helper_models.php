<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{

    use Carbon\Carbon;

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
 * @package App
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert query()
 */
	class Concert extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 */
	class User extends \Eloquent {}
}

