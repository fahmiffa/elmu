@extends('base.layout')
@section('title', 'Dashboard Master Unit')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTablePay({{ json_encode($items) }})">

        <div class="mb-4 flex justify-between items-center gap-2">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />


            <button @click="open = true"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Generate
            </button>

            <div x-show="open" x-transition
                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" style="display: none;">
                <!-- Modal box -->
                <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                    <h2 class="text-xl font-bold mb-4">Generate Tagihan Bulanan</h2>

                    @php
                        $bulan = [
                            'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Juli',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                        ];
                    @endphp
                    <div x-data="generateBill()" class="p-4">
                        {{-- <form @submit.prevent="submitForm">
                            @csrf
                            <label for="bulan">Pilih Bulan</label>
                            <select x-model="selectedMonth" x-data="dropdownSelect" name="bulan" id="bulan" class="my-3">
                                @foreach ($bulan as $index => $item)
                                    <option value="{{ $index + 1 }}" @selected($index + 1 == date('m'))>{{ $item }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                                Buat Tagihan
                            </button>
                        </form> --}}

                        {{-- <!-- Progress bar -->
                        <div class="w-full bg-gray-200 h-4 mt-4 rounded">
                            <div class="bg-green-500 h-4 rounded" :style="{ width: progress + '%' }"></div>
                        </div>
                        <p class="mt-2 text-sm text-gray-700" x-text="progress + '%'"></p> --}}


                        <form method="POST" action="{{ route('dashboard.bill') }}">
                            @csrf

                            <label for="bulan">Pilih Bulan</label>
                            <select x-model="selectedMonth" x-data="dropdownSelect" name="bulan" id="bulan"
                                class="my-3">
                                @foreach ($bulan as $index => $item)
                                    <option value="{{ $index + 1 }}" @selected($index + 1 == date('m'))>{{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            <button
                                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                                Generate
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead>
                    <tr class="bg-orange-500 text-left text-white">
                        <th class="px-4 py-2">No</th>
                        <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Program</th>
                        <th class="px-4 py-2">Kelas</th>
                        <th class="px-4 py-2">Unit</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in paginatedData()" :key="row.id">
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                            <td class="px-4 py-2" x-text="row.reg?.murid?.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="row.reg?.programs.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="row.reg?.class?.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="row.reg?.units?.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="`${row.bulan}/${row.tahun}`"></td>
                            <td class="px-4 py-2" x-text="row.status == 0 ? 'Tagihan' : 'Lunas' "></td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-1">
                                    <a target="_blank" :href="'/dashboard/invoice/' + md5Component(row.id) + ''"
                                        class="text-orange-600 hover:text-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clipboard-list-icon lucide-clipboard-list">
                                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                            <path
                                                d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                            <path d="M12 11h4" />
                                            <path d="M12 16h4" />
                                            <path d="M8 11h.01" />
                                            <path d="M8 16h.01" />
                                        </svg>
                                    </a>

                                    <form x-show="row.status != 1 && row.reg.murid.users.fcm !== null" method="post"
                                        :action="'/dashboard/send/' + md5Component(row.id) + ''">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-700 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-send-icon lucide-send">
                                                <path
                                                    d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
                                                <path d="m21.854 2.147-10.94 10.939" />
                                            </svg>
                                        </button>
                                    </form>

                                    <button x-show="row.status == 0" @click="modal.openModal(row.id)"
                                        class="cursor-pointer text-xs  text-orange-600 font-semibold p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-wallet-icon lucide-wallet">
                                            <path
                                                d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                                            <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                                        </svg>
                                    </button>

                                    <div x-show="modal.activeModal === row.id" x-transition
                                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                                        style="display: none;">
                                        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-90">
                                            <div class="flex justify-between items-start">
                                                <h2 class="text-xl font-bold mb-4"
                                                    x-text="'Pembayaran ' + row.reg.murid.name"></h2>
                                                <button class="item-center p-1" @click="modal.closeModal()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-x-icon lucide-x">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST"
                                                :action="'/dashboard/pembayaran/' + md5Component(row.id) + ''">
                                                @csrf

                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 text-sm font-semibold mb-2">Method</label>
                                                    <select name="via" required
                                                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                                        <option value="">Pilih Opsi</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="transfer">Transfer</option>
                                                    </select>
                                                </div>

                                                <div class="mb-4">
                                                    <label
                                                        class="block text-gray-700 text-sm font-semibold mb-2">Keterangan</label>
                                                    <textarea name="ket" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('ket') }}</textarea>
                                                </div>

                                                <button
                                                    class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                                                    Bayar
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
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
