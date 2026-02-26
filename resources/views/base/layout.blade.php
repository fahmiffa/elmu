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

<body class="bg-gray-100" x-data="layout()" x-init="init()">
    @if (session('status'))
    <div x-data="{ show: false, message: '' }" x-init="message = '{{ session('status') }}';
        show = true;
        setTimeout(() => show = false, 3000);" class="fixed top-4 right-10 z-[100]">
        <div x-show="show" x-transition class="bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
            <span x-text="message"></span>
        </div>
    </div>
    @endif

    @if (session('err'))
    <div x-data="{ show: false, message: '' }" x-init="message = '{{ session('err') }}';
        show = true;
        setTimeout(() => show = false, 3000);" class="fixed top-4 right-10 z-[100]">
        <div x-show="show" x-transition class="bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg">
            <span x-text="message"></span>
        </div>
    </div>
    @endif

    @if (Route::is('dashboard.*'))
    <!-- HEADER -->
    @include('base.header')
    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto mt-6 px-6 grid grid-cols-1 md:grid-cols-4 gap-6 py-2">

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