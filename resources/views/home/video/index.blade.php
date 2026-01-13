@extends('base.layout')
@section('title', 'Dashboard Video')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <input type="text" x-model="search" placeholder="Cari Nama"
            class="flex-1 min-w-[200px] border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

        <select x-model="filterUnit" @change="resetPage()"
            class="border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
            <option value="">Semua Unit</option>
            @foreach($units as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>

        <select x-model="filterProgram" @change="resetPage()"
            class="border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
            <option value="">Semua Program</option>
            @foreach($pro as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
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
                    <th class="px-4 py-2">Video</th>
                    <th class="px-4 py-2">Miska/Maska</th>
                    <th class="px-4 py-2">Murid</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, index) in paginatedData()" :key="row.id">
                    <tr class="border-t border-gray-300">
                        <td class="px-4 py-2"
                            x-text="(perPage === 'all' ? index + 1 : ((currentPage - 1) * perPage) + index + 1)">
                        </td>
                        <td class="px-4 py-2 items-center">
                            <div class="w-50 h-50 overflow-hidden">
                                <div class="font-semibold mb-3" x-text="row.name"></div>
                                <div class="aspect-w-16 aspect-h-9 w-full rounded-lg shadow-lg overflow-hidden">
                                    <iframe class="w-full h-full" :src="row.pile" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2" x-text="row.guru.name"></td>
                        <td class="px-4 py-2" x-text="row.murid.name"></td>
                        {{-- <td class="px-4 py-2">
                                <div class="flex items-center gap-1">
                                    <form :action="'/dashboard/video/' + row.id" method="POST"
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
                            </td> --}}
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