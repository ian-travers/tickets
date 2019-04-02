<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'TicketBeast')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link href="{{ mix('css/app.css', 'build') }}" rel="stylesheet">

    @include('scripts.app')
</head>

<body class="bg-dark">
<div id="app">
    @yield('content')
</div>

@yield('beforeScript')
<script src="{{ mix('js/app.js', 'build') }}"></script>
</body>
</html>
