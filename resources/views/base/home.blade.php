<a href="{{ route('dashboard.reg.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.reg.*') ? 'bg-orange-100' : null }}">
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
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.pay') ? 'bg-orange-100' : null }}">
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
<a href="{{ route('dashboard.akademik') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.akademik') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-graduation-cap-icon lucide-graduation-cap">
                <path
                    d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z" />
                <path d="M22 10v6" />
                <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
            </svg>
        </span> AKademik
    </li>
</a>
<a href="{{ route('dashboard.jadwal.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.jadwal.*') ? 'bg-orange-100' : null }}">
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
<a href="{{ route('dashboard.absensi') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.absensi') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user-round-check-icon lucide-user-round-check">
                <path d="M2 21a8 8 0 0 1 13.292-6" />
                <circle cx="10" cy="8" r="5" />
                <path d="m16 19 2 2 4-4" />
            </svg>
        </span> Absensi
    </li>
</a>
<a href="{{ route('dashboard.level') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.level') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-arrow-up10-icon lucide-arrow-up-1-0">
                <path d="m3 8 4-4 4 4" />
                <path d="M7 4v16" />
                <path d="M17 10V4h-2" />
                <path d="M15 10h4" />
                <rect x="15" y="14" width="4" height="6" ry="2" />
            </svg>
        </span> Level
    </li>
</a>
<a href="{{ route('dashboard.video.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.video.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-tv-minimal-play-icon lucide-tv-minimal-play">
                <path
                    d="M15.033 9.44a.647.647 0 0 1 0 1.12l-4.065 2.352a.645.645 0 0 1-.968-.56V7.648a.645.645 0 0 1 .967-.56z" />
                <path d="M7 21h10" />
                <rect width="20" height="14" x="2" y="3" rx="2" />
            </svg>
        </span> Video
    </li>
</a>
<a href="{{ route('dashboard.raport.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.raport.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-album-icon lucide-album">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                <polyline points="11 3 11 11 14 8 17 11 17 3" />
            </svg>
        </span> Raport
    </li>
</a>
<a href="{{ route('dashboard.report.index') }}">
    <li
        class="flex items-center px-4 py-3 border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.report.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"
                class="lucide lucide-message-square-warning-icon lucide-message-square-warning">
                <path
                    d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                <path d="M12 15h.01" />
                <path d="M12 7v4" />
            </svg>
        </span> Laporan
    </li>
</a>
<a href="{{ route('dashboard.campaign.index') }}">
    <li
        class="flex items-center px-4 py-3 border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.campaign.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-megaphone-icon lucide-megaphone">
                <path
                    d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" />
                <path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14" />
                <path d="M8 6v8" />
            </svg>
        </span> Pengumuman
    </li>
</a>
