<?php

namespace Tests\Feature\Backstage;

use App\Concert;
use App\ConcertFactory;
use App\User;
use Tests\TestCase;

class PublishConcertTest extends TestCase
{
    /** @test */
    function a_promoter_can_publish_their_own_concerts()
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('unpublished')->create([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/backstage/concerts');
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    function a_concert_can_only_be_published_once()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /** @test */
    function a_guest_cannot_publish_concerts()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('unpublished')->create([
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect('/login');
        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function concerts_that_do_not_exist_cannot_be_published()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => 999,
        ]);

        $response->assertStatus(404);
    }
}