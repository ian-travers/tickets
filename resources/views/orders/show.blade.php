@extends('layouts.master')

@section('content')

    @php /* @var \App\Order $order */ @endphp
    <div class="bg-soft p-xs-y-7 full-height">
        <div class="container">
            <div class="constrain-xl m-xs-auto">
                <div class="m-xs-b-6">
                    <div class="flex-baseline flex-spaced p-xs-y-4 border-b">
                        <h1 class="text-xl">Order Summary</h1>
                        <a href="#" class="link-brand-soft">{{ $order->confirmation_number }}</a>
                    </div>
                    <div class="p-xs-y-4 border-b">
                        <p><strong>Order Total: ${{ number_format($order->amount / 100, 2) }}</strong></p>
                        <p class="text-dark-soft">Billed to Card #: **** **** **** {{ $order->card_last_four }}4242</p>
                    </div>
                </div>

                <div class="m-xs-b-7">
                    <h2 class="text-lg wt-normal m-xs-b-4">Your Tickets</h2>

                    @foreach($order->tickets as $ticket)

                    <div class="card m-xs-b-5">
                        <div class="card-section p-xs-y-3 flex-baseline flex-spaced text-light bg-gray">
                            <div>
                                <h1 class="text-xl wt-normal">No Warnings</h1>
                                <p class="text-light-muted">with Cruel Hand and Backtrack</p>
                            </div>
                            <div class="text-right">
                                <strong>General Admission</strong>
                                <p class="text-light-soft">Admin one</p>
                            </div>
                        </div>
                        <div class="card-section border-b">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="media-object">
                                        <div class="media-left">
                                            @icon('calendar', 'text-brand-muted')
                                        </div>
                                        <div class="media-body p-xs-l-4">
                                            <p class="wt-bold">
                                                <time datetime="2019-03-31 19:00:00">
                                                    Sunday, March 31th, 2019
                                                </time>
                                            </p>
                                            <p class="text-dark-soft">Doors at 19:00pm</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="media-object">
                                        <div class="media-left">
                                            @icon('location', 'text-brand-muted')
                                        </div>
                                        <div class="media-body p-xs-l-4">
                                            <p class="wt-bold">Music Hall of Eurodance</p>
                                            <div class="text-dark-soft">
                                                <p>123 Marinenko st.</p>
                                                <p>Polotsk, Belarus 211400</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-section flex-baseline flex-spaced">
                            <p class="text-lg">{{ $ticket->code }}</p>
                            <p>ivan.itpe@example.com</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="text-center text-dark-soft wt-medium">
                <p>Powered by TicketBeast</p>
            </div>
        </div>
    </div>
@endsection

