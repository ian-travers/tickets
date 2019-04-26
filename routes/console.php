<?php

use App\Invitation;
use App\Facades\InvitationCode;

Artisan::command('invite-promoter {email}', function ($email) {
    $invitation = Invitation::create([
        'email' => $email,
        'code' => InvitationCode::generate(),
    ]);
})->describe('Invite a new promoter to create an account.');
