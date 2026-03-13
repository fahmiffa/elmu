<header class="bg-orange-700 text-white">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-5 md:px-6 py-3">
        <div class="text-2xl font-bold">
            ELMU<span class="font-light"></span>
        </div>
        <nav class="space-x-6">
            <a href="{{ route('dashboard.home') }}"
                class="@if (Route::is('dashboard.home')) font-semibold @endif ">Home</a>
            @if(Auth::user()->role == 0)
            <a href="{{ route('dashboard.master.index') }}"
                class="@if (Route::is('dashboard.master.index')) font-semibold @endif ">Master</a>
            @endif
        </nav>
        <div class="flex space-x-4">
            @if(Auth::user()->role == 0)
            <a href="{{ route('dashboard.notifications.index') }}" class="relative inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                </svg>
                @if(Auth::user()->unreadNotifications->count() > 0)
                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 text-[8px] items-center justify-center text-white">
                        {{ Auth::user()->unreadNotifications->count() }}
                    </span>
                </span>
                @endif
            </a>
            @endif
            <a href="{{ route('dashboard.setting') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-settings-icon lucide-settings">
                    <path
                        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
            </a>
            <a class="text-sm" href="{{ route('dashboard.logout') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-log-out-icon lucide-log-out">
                    <path d="m16 17 5-5-5-5" />
                    <path d="M21 12H9" />
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                </svg>
            </a>
        </div>
    </div>
</header>

<div class="bg-orange-600 text-white">
    <div class="max-w-7xl mx-auto flex justify-between px-6 py-3">
        <div>
            <h2 class="text-xl font-semibold">{{ request()->segment(2) ? str_replace("-"," ",ucfirst(request()->segment(2))) : 'Dashboard' }}
            </h2>
            @if (request()->segment(3))
            <p class="text-sm opacity-80">
                @hasSection('breadcrumb')
                @yield('breadcrumb')
                @else
                {{ request()->segment(2) ? str_replace("-"," ",ucfirst(request()->segment(2))) : 'Dashboard' }}
                >
                {{ request()->segment(3) ? str_replace("-"," ",ucfirst(request()->segment(3))) : null }}
                @endif
            </p>
            @endif
        </div>

        <div class="block md:hidden items-center">
            <button @click="toggleSidebarMobile" class="text-gray-50 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-menu-icon lucide-menu">
                    <path d="M4 12h16" />
                    <path d="M4 18h16" />
                    <path d="M4 6h16" />
                </svg>
            </button>
        </div>
    </div>
</div>