<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-100" x-data="layout()" x-init="init()">
    @if (Route::is('dashboard.*'))
        <!-- HEADER -->
        @include('base.header')
        <!-- MAIN CONTENT -->
        <main class="max-w-7xl mx-auto mt-6 px-6 grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- SIDEBAR -->
            @include('base.side')

            <!-- CONTENT -->
            <section class="col-span-3">
                @yield('content')
            </section>


        </main>
    @else
        @yield('content')
    @endif
</body>
@stack('script')

</html>
