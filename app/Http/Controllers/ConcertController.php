<?php

namespace App\Http\Controllers;

use App\Concert;

class ConcertController extends Controller
{
    public function show($id)
    {
        $concert = Concert::whereNotNull('published_at')->findOrFail($id);

        return view('concert.show', compact('concert'));
    }
}
