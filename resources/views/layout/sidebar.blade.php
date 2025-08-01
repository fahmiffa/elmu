<aside x-show="sidebarOpen"
    class="absolute md:relative h-screen w-64 bg-gray-800 text-white flex-shrink-0 transition-all duration-300 z-50">
    <div class="flex items-center justify-between mx-3 text-white">
        <div class="p-4 font-bold text-xl border-b border-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-cpu-icon lucide-cpu">
                <path d="M12 20v2" />
                <path d="M12 2v2" />
                <path d="M17 20v2" />
                <path d="M17 2v2" />
                <path d="M2 12h2" />
                <path d="M2 17h2" />
                <path d="M2 7h2" />
                <path d="M20 12h2" />
                <path d="M20 17h2" />
                <path d="M20 7h2" />
                <path d="M7 20v2" />
                <path d="M7 2v2" />
                <rect x="4" y="4" width="16" height="16" rx="2" />
                <rect x="8" y="8" width="8" height="8" rx="1" />
            </svg>
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
        <a href="{{ route('dashboard.home') }}"
            class="px-4 py-2 hover:bg-gray-700 flex gap-2 font-semibold items-center {{ Route::is('dashboard.home') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-house-icon lucide-house">
                <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                <path
                    d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            </svg>
            <div>Home</div>
        </a>
        <a href="{{ route('dashboard.trans') }}"
            class="{{ Route::is('dashboard.trans') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                <circle cx="8" cy="21" r="1" />
                <circle cx="19" cy="21" r="1" />
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
            </svg>
            <div>Transaksi</div>
        </a>
        <a href="{{ route('dashboard.riwayat') }}"
            class="{{ Route::is('dashboard.riwayat') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-scroll-text-icon lucide-scroll-text">
                <path d="M15 12h-5" />
                <path d="M15 8h-5" />
                <path d="M19 17V5a2 2 0 0 0-2-2H4" />
                <path
                    d="M8 21h12a2 2 0 0 0 2-2v-1a1 1 0 0 0-1-1H11a1 1 0 0 0-1 1v1a2 2 0 1 1-4 0V5a2 2 0 1 0-4 0v2a1 1 0 0 0 1 1h3" />
            </svg>
            <div>Riwayat</div>
        </a>
        <a href="{{ route('dashboard.items.index') }}"
            class="{{ Route::is('dashboard.items.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layout-grid-icon lucide-layout-grid">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg>
            <div>Item</div>
        </a>
        {{-- <a href="{{ route('dashboard.categori.index') }}"
            class="{{ Route::is('dashboard.categori.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-tags-icon lucide-tags">
                <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19" />
                <path
                    d="M9.586 5.586A2 2 0 0 0 8.172 5H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414L8.29 18.29a2.426 2.426 0 0 0 3.42 0l3.58-3.58a2.426 2.426 0 0 0 0-3.42z" />
                <circle cx="6.5" cy="9.5" r=".5" fill="currentColor" />
            </svg>
            <div>Kategori</div>
        </a>
        <a href="{{ route('dashboard.unit.index') }}"
            class="{{ Route::is('dashboard.unit.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-ruler-icon lucide-ruler">
                <path
                    d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z" />
                <path d="m14.5 12.5 2-2" />
                <path d="m11.5 9.5 2-2" />
                <path d="m8.5 6.5 2-2" />
                <path d="m17.5 15.5 2-2" />
            </svg>
            <div>Satuan</div>
        </a> --}}
        <a href="{{ route('dashboard.stok.index') }}"
            class="{{ Route::is('dashboard.stok.index') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-blocks-icon lucide-blocks">
                <path d="M10 22V7a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5a1 1 0 0 0-1-1H2" />
                <rect x="14" y="2" width="8" height="8" rx="1" />
            </svg>
            <div>Inventori</div>
        </a>
        <a href="{{ route('dashboard.laporan') }}"
            class="{{ Route::is('dashboard.laporan') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-clipboard-list-icon lucide-clipboard-list">
                <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                <path d="M12 11h4" />
                <path d="M12 16h4" />
                <path d="M8 11h.01" />
                <path d="M8 16h.01" />
            </svg>
            <div>Laporan</div>
        </a>
        {{-- <a href="#" class="px-4 py-2 hover:bg-gray-700 flex gap-2 font-semibold items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-container-icon lucide-container">
                <path
                    d="M22 7.7c0-.6-.4-1.2-.8-1.5l-6.3-3.9a1.72 1.72 0 0 0-1.7 0l-10.3 6c-.5.2-.9.8-.9 1.4v6.6c0 .5.4 1.2.8 1.5l6.3 3.9a1.72 1.72 0 0 0 1.7 0l10.3-6c.5-.3.9-1 .9-1.5Z" />
                <path d="M10 21.9V14L2.1 9.1" />
                <path d="m10 14 11.9-6.9" />
                <path d="M14 19.8v-8.1" />
                <path d="M18 17.5V9.4" />
            </svg>
            <div>Supplier</div>
        </a> --}}
        @if (auth()->user()->role == 0)
            <a href="{{ route('dashboard.app') }}"
                class="{{ Route::is('dashboard.app') ? 'bg-gray-700' : 'hover:bg-gray-700' }} px-4 py-2 flex gap-2 font-semibold items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-cpu-icon lucide-cpu">
                    <path d="M12 20v2" />
                    <path d="M12 2v2" />
                    <path d="M17 20v2" />
                    <path d="M17 2v2" />
                    <path d="M2 12h2" />
                    <path d="M2 17h2" />
                    <path d="M2 7h2" />
                    <path d="M20 12h2" />
                    <path d="M20 17h2" />
                    <path d="M20 7h2" />
                    <path d="M7 20v2" />
                    <path d="M7 2v2" />
                    <rect x="4" y="4" width="16" height="16" rx="2" />
                    <rect x="8" y="8" width="8" height="8" rx="1" />
                </svg>
                <div>Aplikasi</div>
            </a>
        @endif
    </nav>
</aside>
