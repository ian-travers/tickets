<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invitation
 *
 * @property integer $id
 * @property string $code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App
 */
class Invitation extends Model
{
    public static function findByCode($code): self
    {
        return self::where('code', $code)->first();
    }
}