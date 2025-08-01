@extends('layout.base')
@section('title', 'Dashboard')
@section('content')

    <div x-data="Crudstok()" x-init="fetchData('/dashboard/stok-json');
    fetchItem();">
        <div class="text-gray-800 text-lg bg-gray-50 rounded-2xl shadow-md mx-10 mb-10">
            <div class="p-5">
                <div class="font-semibold text-xl mb-5  border-b-1 inline-block">Data Stok</div>
                <div class="grid grid-cols-1 md:grid-cols-3 w-min-screen">
                    <div class="col-span-1 ">
                        <form method="POST" @submit.prevent="formHandler('/dashboard/stok')">
                            @csrf

                            <div class="mb-4 relative">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Item</label>
                                <input type="hidden" name="item" :value="selectedItem">
                                <input x-model="searchItem" x-on:focus="openItem = true"
                                    x-on:click.outside="openItem = false" type="text" placeholder="Pilih Item"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring"
                                    :class="errors.item ? 'border-red-500' : ''">

                                <ul x-show="openItem" x-transition
                                    class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded shadow max-h-60 overflow-y-auto">
                                    <template x-for="(item, index) in filteredItem()" :key="index">
                                        <li @click="selectItem(item)"
                                            class="px-3 py-2 hover:bg-gray-500 hover:text-white cursor-pointer"
                                            x-text="item.name">
                                        </li>
                                    </template>
                                </ul>

                                <p class="text-red-500 text-sm mt-1" x-text="errors.item?.[0]"></p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Stok</label>
                                <input type="number" min="1" name="stok" x-model="form.stok"
                                    class="w-full p-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-300"
                                    :class="errors.stok ? 'border-red-500' : ''">
                                <p class="text-red-500 text-sm mt-1" x-text="errors.stok?.[0]"></p>
                            </div>

                            <div class="flex items-center">
                                <button type="submit"
                                    class="cursor-pointer bg-gray-800 text-sm hover:bg-gray-900 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline"
                                    :disabled="isLoading" x-text="isLoading ? 'Loading...' : 'Simpan'">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-span-2 p-4">
                        <div class="mb-4 flex justify-between items-center">
                            <input type="text" x-model="search" placeholder="Search..."
                                class="border border-gray-400 bg-gray-50 ring-0 rounded-xl px-3 py-2 w-full focus:outline-none" />
                        </div>
                        <div class="overflow-x-auto  shadow-md">
                            <table class="min-w-full bg-white border border-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-700 text-white text-left">
                                        <th class="px-4 py-2">No</th>
                                        <th class="px-4 py-2">Name</th>
                                        <th class="px-4 py-2">Stok</th>
                                        <th class="px-4 py-2">Waktu</th>
                                        <th class="px-4 py-2">Status</th>
                                        {{-- <th class="cursor-pointer px-4 py-2">Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in paginatedData()" :key="row.id">
                                        <tr class="border-t border-gray-400">
                                            <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1">
                                            </td>
                                            <td class="px-4 py-2" x-text="row.items.name"></td>
                                            <td class="px-4 py-2" x-text="Math.abs(row.count)"></td>
                                            <td class="px-4 py-2" x-text="dateParse(row.created_at)"></td>
                                            <td class="px-4 py-2" x-text="row.status == 1 ? 'Masuk' : 'Keluar'"></td>
                                            {{-- <td class="px-4 py-2 flex items-center gap-1">
                                                <button @click="editItem(row)"
                                                    class="text-gray-800 hover:text-gray-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-pencil-icon lucide-pencil">
                                                        <path
                                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteItem('/dashboard/items',row.id)"
                                                    class="text-gray-800 hover:text-gray-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-trash2-icon lucide-trash-2">
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M3 6h18" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </td> --}}
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

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

        <div class="text-gray-800 text-lg bg-gray-50 rounded-2xl shadow-md mx-10 my-10">
            <div class="p-6 mx-auto">
                <div class="font-semibold text-xl mb-5 border-b-1 inline-block">Total Stok</div>
                <button onclick="toast({ message: 'Hello from toast!', type: 'success' })"
                    class="px-4 py-2 bg-blue-500 text-white">
                    Tes Toast
                </button>

                <div class="overflow-x-auto shadow-md">
                    <table class="min-w-full bg-white border border-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-700 text-white text-left">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Masuk</th>
                                <th class="px-4 py-2">Keluar</th>
                                <th class="px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in sum" :key="index">
                                <tr class="border-t border-gray-400">
                                    <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                    <td class="px-4 py-2" x-text="row.name"></td>
                                    <td class="px-4 py-2" x-text="row.in"></td>
                                    <td class="px-4 py-2" x-text="Math.abs(row.out)"></td>
                                    <td class="px-4 py-2" x-text="row.total"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


@endsection
