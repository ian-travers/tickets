<?php

namespace App\Http\Controllers\Backstage;

use App\Concert;
use App\NullFile;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ConcertController extends Controller
{
    public function index()
    {
        $publishedConcerts = Auth::user()->concerts->filter->isPublished();
        $unpublishedConcerts = Auth::user()->concerts->reject->isPublished();

        return view('backstage.concerts.index', compact('publishedConcerts', 'unpublishedConcerts'));
    }

    public function create()
    {
        return view('backstage.concerts.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:g:ia',
            'venue' => 'required',
            'venue_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'ticket_price' => 'required|numeric|min:5',
            'ticket_quantity' => 'required|numeric|min:1',
            'poster_image' => 'nullable|image|dimensions:min_width=400,ratio=8.5/11',
        ]);

//        dd(request('poster_image')->store('posters', 's3'));

        $concert = Auth::user()->concerts()->create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'additional_info' => request('additional_info'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => (int)request('ticket_quantity'),
            'poster_image_path' => request('poster_image', new NullFile())->store('posters', 'public'),
        ]);

        return redirect()->route('backstage.concert.index');
    }

    public function edit($id)
    {
        /** @var Concert $concert */
        $concert = Auth::user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', compact('concert'));
    }

    public function update($id)
    {
        /** @var \App\Concert $concert */
        $concert = Auth::user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        $this->validate(request(), [
            'title' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:g:ia',
            'venue' => 'required',
            'venue_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'ticket_price' => 'required|numeric|min:5',
            'ticket_quantity' => 'required|integer|min:1',
        ]);


        $concert->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'additional_info' => request('additional_info'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => (int)request('ticket_quantity'),
        ]);

        return redirect()->route('backstage.concert.index');
    }
}
