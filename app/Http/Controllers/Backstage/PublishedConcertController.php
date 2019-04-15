<?php

namespace App\Http\Controllers\Backstage;

use App\Concert;
use App\Http\Controllers\Controller;

class PublishedConcertController extends Controller
{
    public function store()
    {
        $concert = Concert::find(request('concert_id'));

        if ($concert->isPublished()) {
            abort(422);
        }

        $concert->publish();

        return redirect()->route('backstage.concert.index');
    }
}
