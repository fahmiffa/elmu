@extends('base.layout')
@section('title', 'Dashboard Master Guru')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex flex-1 items-center gap-2 w-full">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <select x-model="filterUnit" @change="currentPage = 1"
                class="px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white focus:ring-orange-500 min-w-[150px]">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>

            <select x-model="perPage" @change="resetPage()"
                class="px-3 py-2 border border-gray-300 rounded-xl text-sm bg-white focus:ring-orange-500 min-w-[80px]">
                <option value="10">10</option>
                <option value="100">100</option>
                <option value="all">All</option>
            </select>
        </div>

        <a href="{{ route('dashboard.master.teach.create') }}"
            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-2xl focus:outline-none focus:shadow-outline whitespace-nowrap">
            Tambah Guru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2">No</th>
                    <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                    <th class="px-4 py-2">HP</th>
                    <th class="px-4 py-2">Pendiikan</th>
                    <th class="px-4 py-2">Unit</th>
                    <th class="px-4 py-2">Alamat</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in paginatedData()" :key="row.id">
                    <tr class="border-t border-gray-300">
                        <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                        <td class="px-4 py-2" x-text="row.name"></td>
                        <td class="px-4 py-2" x-text="row.hp"></td>
                        <td class="px-4 py-2" x-text="row.study"></td>
                        <td class="px-4 py-2" x-text="row.unit.name"></td>
                        <td class="px-4 py-2" x-text="row.addr"></td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-1">
                                <a :href="'/dashboard/master/teach/' + row.id"
                                    class="text-blue-600 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-eye-icon lucide-eye">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>

                                <a :href="'/dashboard/master/teach/' + row.id + '/edit'"
                                    class="text-orange-600 hover:text-orange-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-pencil-icon lucide-pencil">
                                        <path
                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                        <path d="m15 5 4 4" />
                                    </svg>
                                </a>

                                <form :action="'/dashboard/master/teach/' + row.id" method="POST"
                                    @submit.prevent="deleteRow($event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-trash2-icon lucide-trash-2">
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
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