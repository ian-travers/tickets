<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Mail\InvitationEmail;
use Mail;

/**
 * Class Invitation
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $email
 * @property string $code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App
 */
class Invitation extends Model
{
    protected $guarded = [];

    public static function findByCode($code): self
    {
        return self::where('code', $code)->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasBeenUsed(): bool
    {
        return $this->user_id !== null;
    }

    public function send()
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }
}
