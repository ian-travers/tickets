@php
    /** @var \App\Concert $concert */
@endphp

<h2>{{ $concert->title }}</h2>
<h2>{{ $concert->subtitle }}</h2>
<p>{{ $concert->formatted_date }}</p>
<p>Doors at {{$concert->date->format('g:ia') }}</p>
<p>{{ number_format($concert->ticket_price/100 , 2) }}</p>
<p>{{ $concert->venue }}</p>
<p>{{ $concert->venue_address }}</p>
<p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
<p>{{ $concert->additional_info }}</p>