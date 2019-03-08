<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertController extends Controller
{
    public function show($id)
    {
        $concert = Concert::find($id);

        return view('concert.show', compact('concert'));
    }
}
