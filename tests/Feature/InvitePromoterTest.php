<?php

namespace Tests\Feature;

use App\Facades\InvitationCode;
use App\Invitation;
use Tests\TestCase;

class InvitePromoterTest extends TestCase
{
    /** @test */
    function inviting_a_promoter_via_cli()
    {
        InvitationCode::shouldReceive('generate')->andReturn('TESTCODE1234');

        $this->artisan('invite-promoter', ['email' => 'john@example.com']);

        $this->assertEquals(1, Invitation::count());

        /** @var Invitation $invitation */
        $invitation = Invitation::first();
        $this->assertEquals('john@example.com', $invitation->email);
        $this->assertEquals('TESTCODE1234', $invitation->code);
    }
}
