@extends('base.layout')
@section('title', 'Dashboard Pendaftaran')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTableReg({{ json_encode($items) }})">

        <div class="mb-4 flex justify-between items-center gap-2">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <a href="{{ route('dashboard.reg.create') }}"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Tambah
            </a>
        </div>

        <div class="flex items-center gap-2 mb-3">
            <span class="text-sm">Show:</span>
            <select x-model="perPage" @change="resetPage()"
                class="border border-gray-300 rounded-lg p-2 focus:outline-[#FF9966]">
                <option value="10">10</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="1000">1000</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead>
                    <tr class="bg-orange-500 text-left text-white">
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2 text-nowrap">Nomor Induk</th>
                        <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Kontrak</th>
                        <th class="px-4 py-2">Program</th>
                        <th class="px-4 py-2">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in paginatedData()" :key="row.id">
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2"
                                x-text="(perPage === 'all' ? index + 1 : ((currentPage - 1) * perPage) + index + 1)">
                            </td>
                            <td class="px-4 py-2" x-text="row.induk"></td>
                            <td class="px-4 py-2" x-text="row.murid.name"></td>
                            <td class="px-4 py-2" x-text="row.kontrak.name"></td>
                            <td class="px-4 py-2">
                                <dl>
                                    <dt x-text="row.programs.name" class="font-semibold capitalize"></dt>
                                    <dt x-text="row.class.name" class="capitalize"></dt>
                                    <dt x-text="row.units.name" class="capitalize"></dt>
                                </dl>
                            </td>
                            <td class="px-4 py-2" x-text="row.waktu"></td>
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
