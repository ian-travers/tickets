<?php

use App\Invitation;
use App\Facades\InvitationCode;
use App\Mail\InvitationEmail;

Artisan::command('invite-promoter {email}', function ($email) {
    $invitation = Invitation::create([
        'email' => $email,
        'code' => InvitationCode::generate(),
    ]);

    Mail::to($email)->send(new InvitationEmail($invitation));
})->describe('Invite a new promoter to create an account.');
