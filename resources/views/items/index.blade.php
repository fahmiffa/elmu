@extends('layout.base')
@section('title', 'Dashboard')
@section('content')

    <div class="text-gray-800 text-lg bg-gray-50 rounded-2xl shadow-md mx-10" x-data="Cruditem()" x-init="fetchData('/dashboard/items-json');
    fetchCat();
    fetchUnit();">
        <div class="grid grid-cols-1 md:grid-cols-3 w-min-screen">
            <div class="p-6 col-span-1">
                <form method="POST" @submit.prevent="formHandler('/dashboard/items')">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                        <input type="text" name="name" x-model="form.name"
                            class="w-full p-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-300"
                            :class="errors.name ? 'border-red-500' : ''">
                        <p class="text-red-500 text-sm mt-1" x-text="errors.name?.[0]"></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Harga <span
                                class="text-xs">/pcs</span></label>
                        <input type="number" min="1" name="price" x-model="form.price"
                            class="w-full p-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-300"
                            :class="errors.price ? 'border-red-500' : ''">
                        <p class=text-sm mt-1" x-text="errors.price?.[0]"></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Stok <span
                                class="text-xs">/pcs</span></label>
                        <input type="number" min="1" name="stok" x-model="form.stok"
                            class="w-full p-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-300"
                            :class="errors.stok ? 'border-red-500' : ''">
                        <p class="text-red-500 text-sm mt-1" x-text="errors.stok?.[0]"></p>
                    </div>

                    {{-- <div class="mb-4 relative">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Kategori</label>
                        <input type="hidden" name="cat_id" :value="selectedCat">
                        <input x-model="searchCat" x-on:focus="openCat = true" x-on:click.outside="openCat = false"
                            type="text" placeholder="Pilih Kategori"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                            :class="errors.cat_id ? 'border-red-500' : ''">

                        <ul x-show="openCat" x-transition
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto">
                            <template x-for="(item, index) in filteredCat()" :key="index">
                                <li @click="selectCat(item)"
                                    class="px-3 py-2 hover:bg-gray-500 hover:text-white cursor-pointer" x-text="item.name">
                                </li>
                            </template>
                        </ul>

                        <p class="text-red-500 text-sm mt-1" x-text="errors.cat_id?.[0]"></p>
                    </div>

                    <div class="mb-4 relative">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                        <input type="hidden" name="unit_id" :value="selectedUnit">
                        <input x-model="searchUnit" name="unit" x-on:focus="openUnit = true" x-on:click.outside="openUnit = false"
                            type="text" placeholder="Pilih Unit"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                            :class="errors.unit_id ? 'border-red-500' : ''">

                        <ul x-show="openUnit" x-transition
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto">
                            <template x-for="(item, index) in filteredUnit()" :key="index">
                                <li @click="selectUnit(item)"
                                    class="px-3 py-2 hover:bg-gray-500 hover:text-white cursor-pointer" x-text="item.name">
                                </li>
                            </template>
                        </ul>

                        <p class="text-red-500 text-sm mt-1" x-text="errors.unit_id?.[0]"></p>
                    </div> --}}

                    <div class="flex items-center">
                        <button type="submit"
                            class="cursor-pointer bg-gray-800 text-sm hover:bg-gray-900 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline"
                            :disabled="isLoading" x-text="isLoading ? 'Loading...' : 'Simpan'">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-6 col-span-2">
                <div class="p-4 max-w-4xl mx-auto">
                    <div class="mb-4 flex justify-between items-center">
                        <input type="text" x-model="search" placeholder="Search..."
                            class="border border-gray-400 bg-gray-50 ring-0 rounded-xl px-3 py-2 w-full focus:outline-none" />
                    </div>

                    <div class="overflow-x-auto rounded-2xl shadow-md">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-800 text-white text-left">
                                    <th class="cursor-pointer px-4 py-2">No</th>
                                    <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Name</th>
                                    <th class="cursor-pointer px-4 py-2">Harga</th>
                                    <th class="cursor-pointer px-4 py-2">Stok</th>
                                    <th class="cursor-pointer px-4 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in paginatedData()" :key="row.id">
                                    <tr class="border-t border-gray-400">
                                        <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                        <td class="px-4 py-2" x-text="row.name"></td>
                                        <td class="px-4 py-2" x-text="numberFormat(row.price)"></td>
                                        <td class="px-4 py-2" x-text="row.stoks"></td>
                                        <td class="px-4 py-2 flex items-center gap-1">
                                            <button @click="editItem(row)" class="text-gray-800 hover:text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-pencil-icon lucide-pencil">
                                                    <path
                                                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </button>
                                            <button @click="deleteItem('/dashboard/items',row.id)"
                                                class="text-gray-800 hover:text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-trash2-icon lucide-trash-2">
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                    <path d="M3 6h18" />
                                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </td>
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
                            class="p-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Prev</button>

                        <span>Page <span x-text="currentPage"></span> of <span x-text="totalPages()"></span></span>

                        <button @click="nextPage()" :disabled="currentPage === totalPages()"
                            class="p-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
