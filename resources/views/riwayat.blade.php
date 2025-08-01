@extends('layout.base')
@section('title', 'Dashboard')
@section('content')
    <div class="flex justify-between mb-5" x-data='dataTable(@json($head, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))'>
        <div class="w-full bg-gray-100 p-4 overflow-y-auto">
            <div class="p-4 max-w-4xl mx-auto">
                <div class="overflow-x-auto rounded-2xl shadow-md">
                    <table class="min-w-full bg-white border border-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-800 text-white text-left">
                                <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">No</th>
                                <th class="cursor-pointer px-4 py-2">Kode</th>
                                <th class="cursor-pointer px-4 py-2">Waktu</th>
                                <th class="cursor-pointer px-4 py-2">Item</th>
                                <th class="cursor-pointer px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index)  in paginatedData()" :key="row.id">
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                    <td class="px-4 py-2" x-text="row.kode"></td>
                                    <td class="px-4 py-2" x-text="dateParse(row.created_at)"></td>
                                    <td class="px-4 py-2">
                                        <table class="w-full">
                                            <template x-for="(col, index) in row.cart" :key="index">
                                                <tr>
                                                    <td class="p-1" x-text="col.items.name"></td>
                                                    <td class="p-1" x-text="col.count"></td>
                                                    <td class="p-1" x-text="rupiah(col.items.price)"></td>
                                                </tr>
                                            </template>
                                        </table>
                                    </td>

                                    <td class="px-4 py-2" x-text="rupiah(row.nominal)"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredData().length === 0">
                                <td colspan="3" class="text-center px-4 py-2 text-gray-500">No results found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-4">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Prev</button>

                    <span>Page <span x-text="currentPage"></span> of <span x-text="totalPages()"></span></span>

                    <button @click="nextPage()" :disabled="currentPage === totalPages()"
                        class="px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>

    </div>
@endsection
