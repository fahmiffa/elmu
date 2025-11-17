<a href="{{ route('dashboard.master.kelas.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.master.kelas.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">


            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-school-icon lucide-school">
                <path d="M14 22v-4a2 2 0 1 0-4 0v4" />
                <path
                    d="m18 10 3.447 1.724a1 1 0 0 1 .553.894V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7.382a1 1 0 0 1 .553-.894L6 10" />
                <path d="M18 5v17" />
                <path d="m4 6 7.106-3.553a2 2 0 0 1 1.788 0L20 6" />
                <path d="M6 5v17" />
                <circle cx="12" cy="9" r="2" />
            </svg>
        </span> Kelas
    </li>
</a>
<a href="{{ route('dashboard.master.unit.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.master.unit.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-layout-grid-icon lucide-layout-grid">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg></span> Unit

    </li>
</a>
<a href="{{ route('dashboard.master.jadwal.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.master.jadwal.*') ? 'bg-orange-100' : null }}">
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
        </span> Jadwal
    </li>
</a>
<a href="{{ route('dashboard.master.zone.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 {{ Route::is('dashboard.master.zone.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-land-plot-icon lucide-land-plot">
                <path d="m12 8 6-3-6-3v10" />
                <path d="m8 11.99-5.5 3.14a1 1 0 0 0 0 1.74l8.5 4.86a2 2 0 0 0 2 0l8.5-4.86a1 1 0 0 0 0-1.74L16 12" />
                <path d="m6.49 12.85 11.02 6.3" />
                <path d="M17.51 12.85 6.5 19.15" />
            </svg>
        </span> Zona

    </li>
</a>
<a href="{{ route('dashboard.master.student.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.student.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-graduation-cap-icon lucide-graduation-cap">
                <path
                    d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z" />
                <path d="M22 10v6" />
                <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
            </svg></span>
        Murid
    </li>
</a>
<a href="{{ route('dashboard.master.teach.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.teach.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-gpu-icon lucide-gpu">
                <path d="M2 21V3" />
                <path d="M2 5h18a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2.26" />
                <path d="M7 17v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-3" />
                <circle cx="16" cy="11" r="2" />
                <circle cx="8" cy="11" r="2" />
            </svg></span>
        Guru
    </li>
</a>
<a href="{{ route('dashboard.master.payment.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer  {{ Route::is('dashboard.master.payment.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-credit-card-icon lucide-credit-card">
                <rect width="20" height="14" x="2" y="5" rx="2" />
                <line x1="2" x2="22" y1="10" y2="10" />
            </svg></span>
        Payment/Kontrak
    </li>
</a>
<a href="{{ route('dashboard.master.program.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.program.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-layers-icon lucide-layers">
                <path
                    d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z" />
                <path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12" />
                <path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17" />
            </svg></span>
        Program/Paket
    </li>
</a>
<a href="{{ route('dashboard.master.user') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.user') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-users-icon lucide-users">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <circle cx="9" cy="7" r="4" />
            </svg></span>
        Akun
    </li>
</a>
<a href="{{ route('dashboard.master.layanan.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.layanan.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-copy-plus-icon lucide-copy-plus">
                <line x1="15" x2="15" y1="12" y2="18" />
                <line x1="12" x2="18" y1="15" y2="15" />
                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
            </svg></span>
        Layanan
    </li>
</a>
<a href="{{ route('dashboard.master.grade.index') }}">
    <li
        class="flex items-center px-4 py-3 border-b border-gray-300 hover:bg-orange-100 cursor-pointer {{ Route::is('dashboard.master.grade.*') ? 'bg-orange-100' : null }}">
        <span class="text-orange-500 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-gauge-icon lucide-gauge">
                <path d="m12 14 4-4" />
                <path d="M3.34 19a10 10 0 1 1 17.32 0" />
            </svg></span>
        Jenjang
    </li>
</a>
