<?php

namespace Tests\Feature\Backstage;

use App\Concert;
use App\ConcertFactory;
use App\OrderFactory;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class ViewPublishedConcertOrdersTest extends TestCase
{
    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished(['user_id' => $user->id]);

        $order = OrderFactory::createForConcert($concert, ['created_at' => Carbon::parse('-11 days')]);

        dd($order->load('tickets'));

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertStatus(200);
        $response->assertViewIs('backstage.published-concert-orders.index');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_unpublished_concerts()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = ConcertFactory::createUnpublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(404);
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_another_published_concert()
    {
        $user = factory(User::class)->create();

        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");
        $response->assertStatus(404);
    }
    /** @test */
    function a_guest_cannot_view_the_orders_of_any_published_concert()
    {
        /** @var Concert $concert */
        $concert = ConcertFactory::createPublished();

        $response = $this->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertRedirect('/login');
    }
}