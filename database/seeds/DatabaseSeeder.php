<?php

use App\Concert;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory(Concert::class)->states('published')->create()->addTickets(10);
    }
}
