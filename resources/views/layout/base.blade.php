<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aplikasi Saya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">
    @auth
        <div x-data="layout()" x-init="init()" class="flex h-screen">
            @if (Route::is('dashboard.*'))
                @include('layout.sidebar')
            @endif
            <div class="flex-1 flex flex-col overflow-hidden">

                @if (Route::is('dashboard.*'))
                    @include('layout.header')
                @endif

                <main class="flex-1 overflow-y-auto py-6" @click="closeSidebarOnMobile">
                    @yield('content')
                </main>

            </div>
        </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')
</body>

</html>
