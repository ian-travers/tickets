<?php

namespace Tests\Feature\Backstage;

use App\Concert;
use App\User;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    public function test_guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();

        $concertA = factory(Concert::class)->create(['user_id' => $user->id]);
        $concertB = factory(Concert::class)->create(['user_id' => $user->id]);
        $concertC = factory(Concert::class)->create(['user_id' => $otherUser->id]);
        $concertD = factory(Concert::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

//        dd($response->original->getData());

        $this->assertTrue($response->original->getData()['concerts']->contains($concertA));
        $this->assertTrue($response->original->getData()['concerts']->contains($concertB));
        $this->assertTrue($response->original->getData()['concerts']->contains($concertD));
        $this->assertEquals(3, $response->original->getData()['concerts']->count());
        $this->assertFalse($response->original->getData()['concerts']->contains($concertC));
    }
}