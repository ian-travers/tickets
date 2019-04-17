@extends('layouts.master')

@section('content')
<div class="full-height bg-soft flex-col">
    <header>
        <nav class="navbar p-xs-y-3">
            <div class="container">
                <div class="navbar-content">
                    <div>
                        <img src="/img/logo.svg" alt="TicketBeast" style="height: 2.5rem;">
                    </div>
                    <div>
                        <a href="{{ route('backstage.concert.index') }}" class="link link-light m-xs-r-6">
                            Your Concerts
                        </a>
                        <form class="inline-block" action="{{ route('auth.logout') }}" method="POST">

                            @csrf
                            <button type="submit" class="link link-light">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="flex-fit">
        @yield('backstageContent')
    </div>

    <footer class="bg-dark p-xs-y-6 text-light-muted">
        <div class="container">
            <p class="text-center">&copy; TicketBeast {{ date('Y') }}</p>
        </div>
    </footer>
</div>
@endsection

