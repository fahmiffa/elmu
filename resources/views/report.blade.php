@extends('layout.base')
@section('title', 'Dashboard')
@section('content')
    <div class="text-gray-800 text-lg sm:max-w-5xl mx-auto p-5" x-data="report()"
     x-init="init('0','1')">
        <form @submit.prevent="submitForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe</label>
                    <select name="tipe" x-model="form.tipe"
                        class="mt-1 block w-full rounded-md p-2 border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        :class="errors.tipe ? 'border-red-500' : ''">
                        <option value="1">Penjualan</option>
                        <option value="2">Stok</option>
                        <option value="3">Item</option>

                    </select>
                    <p class="text-red-500 text-sm mt-1" x-text="errors.tipe?.[0]"></p>
                </div>
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select id="tahun" name="tahun" x-model="form.tahun"
                        class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        :class="errors.tahun ? 'border-red-500' : ''">
                        <option value="0">Semua</option>
                        @foreach ($year as $row)
                            <option value="{{ $row }}">{{ $row }}</option>
                        @endforeach
                    </select>
                    <p class="text-red-500 text-sm mt-1" x-text="errors.tahun?.[0]"></p>
                </div>
                <div class="col-span-1 md:col-span-2 flex items-end">
                    <button type="submit"
                        class="px-4 py-2 text-white rounded-2xl bg-gray-800 text-sm hover:bg-gray-900 transition">Filter</button>
                </div>
            </div>
        </form>
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto my-3 border-gray-300">
            <template x-if="form.tipe === '3'">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-800 text-xs font-semibold text-white uppercase">
                        <tr>
                            <th class="px-4 py-2">No.</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">QTY</th>
                            <th class="px-4 py-2">Harga</th>
                            <th class="px-4 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 px-3">
                        <template x-for="(row, index) in paginatedData()" :key="index">
                            <tr class="border-t border-gray-300">
                                <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                <td class="px-4 py-2" x-text="row.items?.name"></td>
                                <td class="px-4 py-2" x-text="row.total_count"></td>
                                <td class="px-4 py-2" x-text="numberFormat(row.items?.price)"></td>
                                <td class="px-4 py-2 text-end" x-text="numberFormat(row.items?.price * row.total_count)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2 font-semibold text-center" colspan="4">GRANT TOTAL</td>
                            <td class="px-4 py-2 font-semibold text-end" x-text="numberFormat(totalItem())"></td>
                        </tr>
                    </tfoot>
                </table>
            </template>

            <template x-if="form.tipe === '2'">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-800 text-xs font-semibold text-white uppercase">
                        <tr>
                            <th class="px-4 py-2">No.</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Stok</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 px-3">
                        <template x-for="(row, index) in paginatedData()" :key="index">
                            <tr class="border-t border-gray-300">
                                <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                <td class="px-4 py-2" x-text="row.items?.name"></td>
                                <td class="px-4 py-2" x-text="Math.abs(row.count)"></td>
                                 <td class="px-4 py-2" x-text="row.status == 1 ? 'Masuk' : 'Keluar'"></td>
                                <td class="px-4 py-2" x-text="dateLocal(row.created_at)"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredData().length === 0">
                            <td colspan="3" class="text-center px-4 py-2 text-gray-500">No results found.</td>
                        </tr>
                    </tbody>
                </table>
            </template>

            <template x-if="form.tipe === '1'">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-800 text-xs font-semibold text-white uppercase">
                        <tr>
                            <th class="px-4 py-2">No.</th>
                            <th class="px-4 py-2">Kode</th>
                            <th class="px-4 py-2">Waktu</th>
                            <th class="px-4 py-2">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 px-3">
                        <template x-for="(row, index) in paginatedData()" :key="index">
                            <tr class="border-t border-gray-300">
                                <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                                <td class="px-4 py-2" x-text="row.kode"></td>
                                <td class="px-4 py-2" x-text="row.tanggal"></td>
                                <td class="px-4 py-2 text-end" x-text="numberFormat(row.nominal)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2 font-semibold text-center" colspan="3">GRAND TOTAL</td>
                            <td class="px-4 py-2 font-semibold text-end" x-text="numberFormat(totalNominal())"></td>
                        </tr>
                    </tfoot>
                </table>
            </template>
        </div>
    </div>
@endsection
