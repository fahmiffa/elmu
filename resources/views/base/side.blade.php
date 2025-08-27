@if (Route::is('dashboard.master.*'))
    <aside class="bg-white rounded-lg shadow-md overflow-hidden hidden md:block">
        <div class="bg-orange-600 text-white px-4 py-2 font-bold">MENU</div>
        <ul>
            @include('base.master');
        </ul>
    </aside>
@else
    <aside class="bg-white rounded-lg shadow-md overflow-hidden hidden md:block h-100">
        <div class="bg-orange-600 text-white px-4 py-2 font-bold">MENU</div>
        <ul>
            @include('base.home');
        </ul>
    </aside>
@endif

<aside x-show="sidebarOpen"
    class="absolute md:hidden h-screen w-64 bg-white text-black flex-shrink-0 transition-all duration-300 z-50 left-0 top-0 shadow-2xl translate-x-0">
    <div class="flex items-center justify-between mx-3">
        <div class="p-4 font-bold text-xl border-b border-gray-50">
            {{ Route::is('dashboard.master.*') ? 'Master' : 'Home' }}
        </div>
        <button @click="toggleSidebar" class="hover:text-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-panel-left-close-icon lucide-panel-left-close">
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M9 3v18" />
                <path d="m16 15-3-3 3-3" />
            </svg>
        </button>
    </div>
    <nav class="p-4 space-y-2">
        @if (Route::is('dashboard.master.*'))
            @include('base.master');
        @else
            @include('base.home');
        @endif
    </nav>
</aside>
