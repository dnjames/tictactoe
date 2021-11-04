<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <title>{{ config('app.name', 'Tic Tac Toe') }}</title>
</head>
<body>
    <div class="container">

        <div class="row">
            <div class="col mt-3">

                <h1 class="text-center">{{ config('app.name', 'Tic Tac Toe') }}</h1>

            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col mt-3">

                @yield('content')

            </div>
        </div>

    </div>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
