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

    public function test_promoters_can_view_a_list_of_their_concerts()
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = factory(User::class)->create();
        $concerts = factory(Concert::class, 3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

//        dd($response->original->getData());

        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[0]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[1]));
        $this->assertTrue($response->original->getData()['concerts']->contains($concerts[2]));
        $this->assertEquals(3, $response->original->getData()['concerts']->count());
    }
}