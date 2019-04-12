<?php

namespace Tests\Feature\Backstage;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PromoterLoginTest extends TestCase
{
    /** @test  */
    function showing_the_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /** @test  */
    function logging_in_with_valid_credentials()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('111111'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => '111111',
        ]);

        $response->assertRedirect('/backstage/concerts/new');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test  */
    function logging_in_with_invalid_credentials()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('111111'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test  */
    function logging_in_with_an_account_that_does_not_exist()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test  */
    function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}