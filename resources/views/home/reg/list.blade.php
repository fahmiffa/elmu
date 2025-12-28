@extends('base.layout')
@section('title', 'Dashboard Akademik')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTableReg({{ json_encode($items) }})">

        <div class="mb-4 flex justify-between items-center gap-2">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />
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
                        <th class="px-4 py-2">Program</th>
                        <th class="px-4 py-2">Status</th>
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
                            <td class="px-4 py-2">
                                <dl>
                                    <dt x-text="row.programs.name" class="font-semibold capitalize"></dt>
                                    <dt x-text="row.class.name" class="capitalize"></dt>
                                    <dt x-text="row.units.name" class="capitalize"></dt>
                                </dl>
                            </td>
                            <td class="px-4 py-2 items-center flex-row text-center">
                                <div x-show="row.done === 0">
                                    <button @click="openModal(row)"
                                        class="bg-orange-500 text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:bg-orange-600"
                                        type="button" x-text="row.status"></button>
                                </div>

                                <div x-show="row.done === 1">
                                    <button @click="openModal(row)"
                                        class="bg-green-500 text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:bg-green-600"
                                        type="button" x-text="row.status"></button>
                                </div>

                                <div x-show="row.done === 2">
                                    <button @click="openModal(row)"
                                        class="bg-red-500 text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:bg-red-600"
                                        type="button" x-text="row.status"></button>
                                </div>

                                <div x-show="row.done === 3">
                                    <button @click="openModal(row)"
                                        class="bg-gray-800 text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:bg-gray-800"
                                        type="button" x-text="row.status"></button>
                                </div>

                                <div x-text="row.note"></div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData().length === 0">
                        <td colspan="3" class="text-center px-4 py-2 text-gray-500">No results found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

           <div x-show="modalOpen" x-cloak style="background-color: rgba(0,0,0,0.5);"
                class="fixed inset-0 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative" @click.away="closeModal()">
                    <h2 class="text-sm font-bold mb-4">Update Status</h2>
                    <form method="POST" :action="'/dashboard/status/' + selectedItem?.id + ''">
                        @csrf
                        <input type="hidden" name="id" :value="selectedItem?.id">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                            <select class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" name="status" required>
                            <option value="">Pilih</option>
                            <option value="3">Off (Keluar)</option>
                            <option value="2">Cuti</option>
                            <option value="1">Lulus</option>
                            <option value="0">Aktif</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Keterangan</label>
                            <textarea name="keterangan" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <button
                            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                            Simpan
                        </button>
                    </form>
                    <button
                        class="mt-6 bg-gray-800 text-white text-xs cursor-pointer px-3 py-2 rounded-2xl hover:bg-gray-900 float-end"
                        @click="closeModal()" type="button">Close</button>
                </div>
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
