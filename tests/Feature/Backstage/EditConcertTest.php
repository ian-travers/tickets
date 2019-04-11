<?php

namespace Tests\Feature\Backstage;

use App\User;
use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class EditConcertTest extends TestCase
{
    /** @test */
    function promoters_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $user->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create(['user_id' => $user->id]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(404);
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get("/backstage/concerts/999/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_edit_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_info' => 'Old additional information',
            'date' => Carbon::parse('2019-01-01 5:00pm '),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}", [
            'user_id' => $user->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_info' => 'New additional information',
            'date' => '2019-04-22',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
        ]);

        $response->assertRedirect("/backstage/concerts");

        tap($concert->fresh(), function ($concert){
            $this->assertEquals('New title', $concert->title);
            $this->assertEquals('New subtitle', $concert->subtitle);
            $this->assertEquals('New additional information', $concert->additional_info);
            $this->assertEquals(Carbon::parse('2019-04-22 8:00pm'), $concert->date);
            $this->assertEquals('New venue', $concert->venue);
            $this->assertEquals('New address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('99999', $concert->zip);
            $this->assertEquals(7250, $concert->ticket_price);
        });
    }

    /** @test */
    function promoters_cannot_edit_other_unpublished_concerts()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var User $otherUser */
        $otherUser = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = factory(Concert::class)->create([
            'user_id' => $otherUser->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_info' => 'Old additional information',
            'date' => Carbon::parse('2019-01-01 5:00pm '),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}", [
            'user_id' => $user->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_info' => 'New additional information',
            'date' => '2019-04-22',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
        ]);

        $response->assertStatus(404);

        tap($concert->fresh(), function ($concert){
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_info);
            $this->assertEquals(Carbon::parse('2019-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    function promoters_cannot_edit_published_concerts()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = factory(Concert::class)->states('published')->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_info' => 'Old additional information',
            'date' => Carbon::parse('2019-01-01 5:00pm '),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}", [
            'user_id' => $user->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_info' => 'New additional information',
            'date' => '2019-04-22',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
        ]);

        $response->assertStatus(403);

        tap($concert->fresh(), function ($concert){
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_info);
            $this->assertEquals(Carbon::parse('2019-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }

    /** @test */
    function guests_cannot_edit_concerts()
    {
        $this->withoutExceptionHandling([\Illuminate\Auth\AuthenticationException::class]);

        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Concert $concert */
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_info' => 'Old additional information',
            'date' => Carbon::parse('2019-01-01 5:00pm '),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->patch("/backstage/concerts/{$concert->id}", [
            'user_id' => $user->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_info' => 'New additional information',
            'date' => '2019-04-22',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
        ]);

        $response->assertRedirect('/login');

        tap($concert->fresh(), function ($concert){
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_info);
            $this->assertEquals(Carbon::parse('2019-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('00000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }
}