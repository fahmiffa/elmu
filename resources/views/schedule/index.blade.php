@extends('base.layout')
@section('title', 'Dashboard Master Unit')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

        <div class="mb-4 flex justify-between items-center gap-2">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />


            <button @click="open = true"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Generate
            </button>

            <div x-show="open" class="fixed inset-0 flex items-center justify-center" style="display: none;">
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
                        <th class="px-4 py-2">Program Belajar</th>
                        <th class="px-4 py-2">Kelas</th>
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
                            <td class="px-4 py-2" x-text="row.reg?.paket?.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="row.reg?.class?.name ?? '-'"></td>
                            <td class="px-4 py-2" x-text="`${row.bulan}/${row.tahun}`"></td>
                            <td class="px-4 py-2" x-text="row.status == 0 ? 'Tagihan' : 'Lunas' "></td>
                            <td class="px-4 py-2 flex items-center gap-1">
                                <a :href="'/dashboard/invoice/' + row.id + ''"
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
