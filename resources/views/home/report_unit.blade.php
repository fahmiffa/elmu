@extends('base.layout')

@section('title', 'Laporan Unit')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Laporan Pembayaran & Layanan per Unit</h2>

        <form action="{{ route('dashboard.report.unit') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="bulan" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    @foreach($bulanMap as $key => $name)
                    <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="tahun" class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition shadow-sm text-sm font-medium">
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border">Unit</th>
                    <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border">Total Siswa</th>
                    <th colspan="2" class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border bg-blue-50">Pembayaran Bulanan</th>
                    <th colspan="2" class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border bg-green-50">Layanan</th>
                    <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border bg-orange-50">Total Bayar</th>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-center text-xs font-medium text-blue-700 border bg-blue-50/50">Lunas</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-red-700 border bg-blue-50/50">Belum</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-green-700 border bg-green-50/50">Lunas</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-red-700 border bg-green-50/50">Belum</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border">
                        {{ $item->name }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-700 border">
                        {{ number_format($item->total_siswa) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-blue-600 font-medium border">
                        Rp {{ number_format($item->paid_monthly, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-500 border">
                        Rp {{ number_format($item->unpaid_monthly, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-green-600 font-medium border">
                        Rp {{ number_format($item->paid_service, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-500 border">
                        Rp {{ number_format($item->unpaid_service, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-orange-600 font-bold border bg-orange-50/30">
                        Rp {{ number_format($item->paid_monthly + $item->paid_service, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-4 text-sm border font-bold">TOTAL KESELURUHAN</td>
                    <td class="px-4 py-4 text-center text-sm border">{{ number_format($items->sum('total_siswa')) }}</td>
                    <td class="px-4 py-4 text-right text-sm border text-blue-700">Rp {{ number_format($items->sum('paid_monthly'), 0, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right text-sm border text-red-600">Rp {{ number_format($items->sum('unpaid_monthly'), 0, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right text-sm border text-green-700">Rp {{ number_format($items->sum('paid_service'), 0, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right text-sm border text-red-600">Rp {{ number_format($items->sum('unpaid_service'), 0, ',', '.') }}</td>
                    <td class="px-4 py-4 text-right text-sm border text-orange-700 font-extrabold bg-orange-100">
                        Rp {{ number_format($items->sum('paid_monthly') + $items->sum('paid_service'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection