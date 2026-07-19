<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        /* Scrollbar disembunyikan */
        .scroll-hidden::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        /* Saat hover atau focus, tampilkan scrollbar */
        .scroll-show:hover::-webkit-scrollbar,
        .scroll-show:focus::-webkit-scrollbar {
            width: 6px;
        }

        .scroll-show:hover::-webkit-scrollbar-thumb,
        .scroll-show:focus::-webkit-scrollbar-thumb {
            background: #c4c4c4;
            border-radius: 10px;
        }
    </style>

</head>

<body class="bg-gray-100 overflow-hidden h-screen" x-data="layout()" x-init="init()">
    <div x-data="sweetAlert()" x-init="
        @if (session('status')) toast('{{ session('status') }}', 'success'); @endif
        @if (session('err')) toast('{{ session('err') }}', 'error'); @endif
    "></div>

    @if (Route::is('dashboard.*'))
    <div class="flex flex-col h-full w-full">
        <!-- HEADER -->
        <div class="flex-none z-40">
            @include('base.header')
        </div>

        <!-- MAIN CONTENT (Takes remaining height) -->
        <main class="flex-1 min-h-0 grid grid-cols-1 md:grid-cols-12">

            <!-- SIDEBAR (Fixed flex layout, scrollable) -->
            <div class="md:col-span-3 lg:col-span-2 hidden md:block overflow-y-auto scroll-show scroll-hidden px-4 py-4">
                @include('base.side')
            </div>

            <!-- CONTENT (Scrollable area) -->
            <section class="md:col-span-9 lg:col-span-10 overflow-y-auto scroll-show scroll-hidden px-4 md:px-8 py-4">
                @yield('content')
            </section>

        </main>
    </div>
    @else
    @yield('content')
    @endif
</body>
@stack('script')

</html>