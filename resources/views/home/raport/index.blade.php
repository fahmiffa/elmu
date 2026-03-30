@extends('base.layout')
@section('title', 'Dashboard Raport')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

        <div class="mb-4 flex justify-end items-center gap-2">
            <a href="{{ route('dashboard.raport.create') }}"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Tambah
            </a>
        </div>

        <div class="flex items-center gap-2 mb-3">
            <span class="text-sm">Show:</span>
            <select x-model="perPage" @change="resetPage()"
                class="border border-gray-300 rounded-lg p-1 focus:outline-[#FF9966]">
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
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Panggilan</th>
                        <th class="px-4 py-2">Raport</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in paginatedData()" :key="row.id">
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2"
                                x-text="(perPage === 'all' ? index + 1 : ((currentPage - 1) * perPage) + index + 1)">
                            </td>
                            <td class="px-4 py-2" x-text="row.name"></td>
                            <td class="px-4 py-2" x-text="row.murid.name"></td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    {{-- Preview PDF --}}
                                    <a :href="'/dashboard/raport/preview/' + row.id" target="_blank" class="text-blue-600 hover:text-blue-800" title="Preview PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a :href="'/dashboard/raport/' + row.id + '/edit'" class="text-orange-600 hover:text-orange-800" title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form :action="'/dashboard/raport/' + row.id" method="POST" @submit.prevent="deleteRow($event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                                                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData().length === 0">
                        <td colspan="5" class="text-center px-4 py-2 text-gray-500 italic">Data belum tersedia.</td>
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
