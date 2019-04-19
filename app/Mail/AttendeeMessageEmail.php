<?php

namespace App\Mail;

use App\AttendeeMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendeeMessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $attendeeMessage;

    /**
     * Create a new message instance.
     *
     * @param AttendeeMessage $attendeeMessage
     */
    public function __construct(AttendeeMessage $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->attendeeMessage->subject)
            ->text('emails.attendee-message-email');
    }
}
