<?php

use App\Invitation;
use App\Facades\InvitationCode;

Artisan::command('invite-promoter {email}', function ($email) {
    Invitation::create([
        'email' => $email,
        'code' => InvitationCode::generate(),
    ])->send();
})->describe('Invite a new promoter to create an account.');
