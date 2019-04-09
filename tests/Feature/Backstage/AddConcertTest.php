<?php

namespace Tests\Feature\Backstage;

use App\User;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    public function test_promoter_can_view_the_add_concert_form()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }


    public function test_guests_cannot_view_the_add_concert_form()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

}