<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'フリマアプリ') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/header.css') }}">

    @yield('css')
</head>

<body>
    @include('components.header')

    <main>
        @yield('content')
    </main>

</body>

</html>
