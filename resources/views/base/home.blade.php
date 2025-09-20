<a href="{{ route('dashboard.reg') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.reg.*') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-file-input-icon lucide-file-input">
                <path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4" />
                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                <path d="M2 15h10" />
                <path d="m9 18 3-3-3-3" />
            </svg>
        </span> Pendaftaran
    </li>
</a>
<a href="{{ route('dashboard.pay') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.pay') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-banknote-icon lucide-banknote">
                <rect width="20" height="12" x="2" y="6" rx="2" />
                <circle cx="12" cy="12" r="2" />
                <path d="M6 12h.01M18 12h.01" />
            </svg>
        </span> Pembayaran
    </li>
</a>
<a href="{{ route('dashboard.jadwal.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.jadwal.*') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-calendar-cog-icon lucide-calendar-cog">
                <path d="m15.228 16.852-.923-.383" />
                <path d="m15.228 19.148-.923.383" />
                <path d="M16 2v4" />
                <path d="m16.47 14.305.382.923" />
                <path d="m16.852 20.772-.383.924" />
                <path d="m19.148 15.228.383-.923" />
                <path d="m19.53 21.696-.382-.924" />
                <path d="m20.772 16.852.924-.383" />
                <path d="m20.772 19.148.924.383" />
                <path d="M21 11V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6" />
                <path d="M3 10h18" />
                <path d="M8 2v4" />
                <circle cx="18" cy="18" r="3" />
            </svg>
        </span> Penjadwalan
    </li>
</a>
<a href="{{ route('dashboard.study') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.study.*') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-book-open-text-icon lucide-book-open-text">
                <path d="M12 7v14" />
                <path d="M16 12h2" />
                <path d="M16 8h2" />
                <path
                    d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                <path d="M6 12h2" />
                <path d="M6 8h2" />
            </svg>
        </span> Pembelajaran
    </li>
</a>
<a href="{{ route('dashboard.slide.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.slide.*') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-boxes-icon lucide-boxes">
                <path
                    d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z" />
                <path d="m7 16.5-4.74-2.85" />
                <path d="m7 16.5 5-3" />
                <path d="M7 16.5v5.17" />
                <path
                    d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z" />
                <path d="m17 16.5-5-3" />
                <path d="m17 16.5 4.74-2.85" />
                <path d="M17 16.5v5.17" />
                <path
                    d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z" />
                <path d="M12 8 7.26 5.15" />
                <path d="m12 8 4.74-2.85" />
                <path d="M12 13.5V8" />
            </svg>
        </span> Level
    </li>
</a>
<a href="{{ route('dashboard.slide.index') }}">
    <li
        class="flex items-center px-4 py-3 border-gray-300 hover:bg-gray-100 {{ Route::is('dashboard.slide') ? 'bg-gray-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-message-square-warning-icon lucide-message-square-warning">
                <path
                    d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                <path d="M12 15h.01" />
                <path d="M12 7v4" />
            </svg>
        </span> Laporan
    </li>
</a>
