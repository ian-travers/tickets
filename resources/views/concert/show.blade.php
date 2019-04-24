@extends('layouts.master')

@section('beforeScript')
    <script src="https://checkout.stripe.com/checkout.js"></script>
@endsection

@section('content')
    <div class="bg-soft p-xs-y-7 full-height">
        <div class="container">

            @php /** @var \App\Concert $concert */ @endphp
            @if($concert->hasPoster())
                @include('concert.partial.card-with-poster', compact('concert'))
            @else
                @include('concert.partial.card-no-poster', compact('concert'))
            @endif
            <div class="text-center text-dark-soft wt-medium">
                <p>Powered by TicketBeast</p>
            </div>
        </div>
    </div>
@endsection

@section('beforeScripts')
    <script src="https://checkout.stripe.com/checkout.js"></script>
@endsection
