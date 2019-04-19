<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AttendeeMessage
 *
 * @property integer $id
 * @property integer $concert_id
 * @property string $subject
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App
 */
class AttendeeMessage extends Model
{
    protected $guarded = [];
}
