@extends('base.layout')
@section('title', 'User')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <select x-model="filterRole" @change="resetPage()"
                class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Tipe</option>
                <option value="3">Guru</option>
                <option value="2">User</option>
                <option value="4">Operator</option>
            </select>
        </div>

        <a href="{{ route('dashboard.master.user.create') }}"
            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
            Tambah
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2">No</th>
                    <th class="cursor-pointer px-4 py-2">Username</th>
                    <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">HP</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Zona</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in paginatedData()" :key="row.id">
                    <tr class="border-t border-gray-300">
                        <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                        <td class="px-4 py-2" x-text="row.name"></td>
                        <td class="px-4 py-2" x-text="row.data ? row.data.name : row.name"></td>
                        <td class="px-4 py-2" x-text="row.email"></td>
                        <td class="px-4 py-2" x-text="row.nomor"></td>
                        <td class="px-4 py-2" x-text="row.roles"></td>
                        <td class="px-4 py-2 text-nowrap" x-text="row.zone ? row.zone.name : '-'"></td>
                        <td class="px-4 py-2" x-text="row.state"></td>
                        <td class="px-4 py-2 flex items-center gap-1">
                            <a :href="'/dashboard/master/user/' + md5Component(row.id) + '/detail'"
                                class="text-orange-600 hover:text-orange-700" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-user-round-cog-icon lucide-user-round-cog">
                                    <path d="m14.305 19.53.923-.382" />
                                    <path d="m15.228 16.852-.923-.383" />
                                    <path d="m16.852 15.228-.383-.923" />
                                    <path d="m16.852 20.772-.383.924" />
                                    <path d="m19.148 15.228.383-.923" />
                                    <path d="m19.53 21.696-.382-.924" />
                                    <path d="M2 21a8 8 0 0 1 10.434-7.62" />
                                    <path d="m20.772 16.852.924-.383" />
                                    <path d="m20.772 19.148.924.383" />
                                    <circle cx="10" cy="8" r="5" />
                                    <circle cx="18" cy="18" r="3" />
                                </svg>
                            </a>
                            <a x-show="row.role == 4" :href="'/dashboard/master/user/' + md5Component(row.id) + '/edit'"
                                class="text-orange-600 hover:text-orange-600" title="Edit Data">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                    <path d="m15 5 4 4" />
                                </svg>
                            </a>
                            <a :href="'/dashboard/master/user/' + md5Component(row.id) + '/password'"
                                class="text-orange-600 hover:text-blue-700" title="Ubah Password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key-round">
                                    <path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z" />
                                    <circle cx="16.5" cy="7.5" r=".5" fill="currentColor" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                </template>
                <tr x-show="filteredData().length === 0">
                    <td colspan="3" class="text-center px-4 py-2 text-gray-500">No results found.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center mt-4">
        <button @click="prevPage()" :disabled="currentPage === 1"
            class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Prev</button>

        <span>Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages()"></span></span>

        <button @click="nextPage()" :disabled="currentPage === totalPages()"
            class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Next</button>
    </div>
</div>
@endsection