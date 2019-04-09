<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PromoterLoginTest extends DuskTestCase
{

    public function test_login_is_successfully()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('111111'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'jane@example.com')
                ->type('password', '111111')
                ->press('Log in')
                ->assertPathIs('/backstage/concerts/new');
        });

        $user->delete();
    }

    public function test_loggin_in_with_invalid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('111111'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'jane@example.com')
                ->type('password', 'wrong')
                ->press('Log in')
                ->assertPathIs('/login')
                ->assertSee('credentials do not match');
        });

        $user->delete();
    }
}