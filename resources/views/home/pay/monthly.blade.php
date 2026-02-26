@extends('base.layout')
@section('title', 'Pembayaran Bulanan')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    <div x-data="{ tabStatus: 'tagihan' }">
        <div class="flex border-b border-gray-300">
            <button class="px-4 py-2 -mb-px text-sm font-medium border-b-2 cursor-pointer"
                :class="tabStatus === 'tagihan' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-600 hover:text-orange-500'"
                @click="tabStatus = 'tagihan'">
                Tagihan
            </button>
            <button class="px-4 py-2 -mb-px text-sm font-medium border-b-2 cursor-pointer"
                :class="tabStatus === 'riwayat' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-600 hover:text-orange-500'"
                @click="tabStatus = 'riwayat'">
                Riwayat
            </button>
        </div>

        <div class="mt-4" x-data="dataTablePay({{ json_encode($items) }})">
            {{-- Filtering logic override for tabs --}}
            <div x-init="
                $watch('tabStatus', value => {
                    currentPage = 1;
                });
                
                // Override filteredData to include tabStatus filtering
                const originalFilteredData = filteredData;
                filteredData = function() {
                    let data = originalFilteredData.call(this);
                    if (tabStatus === 'riwayat') {
                        return data.filter(row => row.status == 1);
                    } else {
                        return data.filter(row => row.status != 1);
                    }
                }
            ">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2 flex-1">
                        <input type="text" x-model="search" placeholder="Cari Nama / Panggilan"
                            class="w-full md:w-1/3 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

                        <select x-model="filterUnit" @change="resetPage()"
                            class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                            <option value="">Semua Unit</option>
                            @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>

                        <select x-model="filterProgram" @change="resetPage()"
                            class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                            <option value="">Semua Program</option>
                            @foreach($pro as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(Auth::user()->role != 4)
                    <button @click="open = true"
                        class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                        Generate
                    </button>
                    @endif

                    <div x-show="open" x-cloak x-transition
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <!-- Modal box -->
                        <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90">
                            <h2 class="text-xl font-bold mb-4">Generate Tagihan Bulanan</h2>

                            @php
                            $bulan = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                            ];
                            @endphp
                            <div x-data="generateBill()" class="p-4">
                                <form method="POST" action="{{ route('dashboard.bill') }}">
                                    @csrf
                                    <label for="bulan">Pilih Bulan</label>
                                    <select x-model="selectedMonth" x-data="dropdownSelect" name="bulan"
                                        id="bulan" class="my-3">
                                        @foreach ($bulan as $index => $item)
                                        <option value="{{ $index + 1 }}" @selected($index + 1==date('m'))>
                                            {{ $item }}
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
                                <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Panggilan</th>
                                <th class="px-4 py-2">Program/Unit</th>
                                <th class="px-4 py-2 text-nowarp">Tempo</th>
                                <th class="px-4 py-2">Waktu</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Tipe</th>
                                <th class="px-4 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in paginatedData()" :key="row.id">
                                <tr class="border-t border-gray-300">
                                    <td class="px-4 py-2"
                                        x-text="(perPage === 'all' ? index + 1 : ((currentPage - 1) * perPage) + index + 1)">
                                    </td>
                                    <td class="px-4 py-2 text-nowarp" x-text="row.reg?.murid?.name ?? '-'"></td>
                                    <td class="px-4 py-2 text-nowarp" x-text="row.reg?.murid?.nama_panggilan ?? ''"></td>
                                    <td class="px-4 py-2">
                                        <div class="text-xs">
                                            <div class="font-semibold" x-text="row.reg?.units?.name ?? '-'"></div>
                                            <div x-text="row.reg?.programs?.name ?? '-'"></div>
                                            <div class="text-gray-500" x-text="row.reg?.class?.name ?? '-'"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-nowarp" x-text="row.tempo ?? '-'"></td>
                                    <td class="px-4 py-2" x-text="`${row.bulan}/${row.tahun}`"></td>
                                    <td class="px-4 py-2" x-text="formatNumber(row.total)"></td>
                                    <td class="px-4 py-2"
                                        x-text="
                                        row.status == 0 ? 'Tagihan' : 
                                        (row.status == 2 ? 'Menunggu Pembayaran' : 
                                        (row.status == 1 ? 'Lunas' : 'Kadaluarsa'))
                                        ">
                                    </td>
                                    <td class="px-4 py-2" x-text="row.tipe"></td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center justify-center gap-1">
                                            <form x-show="row.status != 1 && row.reg.murid.users.fcm !== null"
                                                method="post"
                                                :action="'/dashboard/send/' + md5Component(row.id) + '/bul'">
                                                @csrf
                                                <button type="submit"
                                                    class="text-orange-600 hover:text-orange-700 cursor-pointer" title="Kirim Notifikasi">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" />
                                                        <path d="m21.854 2.147-10.94 10.939" />
                                                    </svg>
                                                </button>
                                            </form>

                                            <button x-show="row.status != 1" @click="modal.openModal(row.id)"
                                                class="cursor-pointer text-orange-600 font-semibold p-1" title="Bayar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                                                    <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                                                </svg>
                                            </button>

                                            <a x-show="row.status == 1" :href="'/dashboard/invoice/' + md5Component(row.id)" target="_blank"
                                                class="text-blue-600 p-1" title="Cetak Invoice">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M6 9V2h12v7" />
                                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                                    <rect width="12" height="8" x="6" y="14" />
                                                </svg>
                                            </a>

                                            <div x-show="modal.activeModal === row.id" x-cloak x-transition
                                                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                                <div @click.away="modal.closeModal()"
                                                    class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
                                                    <div class="flex justify-between items-start">
                                                        <h2 class="text-xl font-bold mb-4"
                                                            x-text="'Pembayaran ' + row.reg.murid.name"></h2>
                                                        <button class="item-center p-1" @click="modal.closeModal()">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M18 6 6 18" />
                                                                <path d="m6 6 12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <form method="POST"
                                                        :action="'/dashboard/pembayaran/' + md5Component(row.id) + '/bul'">
                                                        @csrf
                                                        <div class="mb-4">
                                                            <label
                                                                class="block text-gray-700 text-sm font-semibold mb-2">Method</label>
                                                            <select name="via" required
                                                                class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                                                <option value="">Pilih Opsi</option>
                                                                <option value="cash">Cash</option>
                                                                <option value="transfer">Transfer</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label
                                                                class="block text-gray-700 text-sm font-semibold mb-2">Keterangan</label>
                                                            <textarea name="ket" class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('ket') }}</textarea>
                                                        </div>

                                                        <button
                                                            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                                                            Bayar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredData().length === 0">
                                <td colspan="10" class="text-center px-4 py-4 text-gray-500">Data tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between items-center mt-4 text-sm">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Prev</button>

                    <span class="text-gray-600">Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages()"></span></span>

                    <button @click="nextPage()" :disabled="currentPage === totalPages()"
                        class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection