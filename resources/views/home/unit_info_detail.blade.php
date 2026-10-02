@extends('base.layout')

@section('title', 'Detail Unit')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Detail Unit: {{ $unit->name }}</h2>
        <a href="{{ route('dashboard.unit.info') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition text-sm">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Unit -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Unit</h3>
            <div class="space-y-3">
                <div>
                    <span class="block text-sm font-medium text-gray-500">Kode Unit</span>
                    <span class="block text-base text-gray-900">{{ $unit->kode ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Nama Unit</span>
                    <span class="block text-base text-gray-900">{{ $unit->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Alamat</span>
                    <span class="block text-base text-gray-900">{{ $unit->addr ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Informasi Zona -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Zona</h3>
            @if($unit->zone && $unit->zone->count() > 0)
                <div class="space-y-4">
                    @foreach($unit->zone as $zone)
                        <div class="bg-white p-3 rounded border border-gray-100 shadow-sm">
                            <div class="mb-2">
                                <span class="block text-sm font-medium text-gray-500">Nama Zona</span>
                                <span class="block text-base text-gray-900">{{ $zone->name ?? '-' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="block text-sm font-medium text-gray-500">PIC</span>
                                <span class="block text-base text-gray-900">{{ $zone->pic ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-500">No. HP / Kontak</span>
                                <span class="block text-base text-gray-900">{{ $zone->hp ?? '-' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Unit ini belum ditugaskan ke zona manapun.</p>
            @endif
        </div>
    </div>

    <!-- Bagian Grafik -->
    <div class="mt-8">
        <div class="flex justify-end mb-4">
            <form method="GET" class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                <label class="text-sm font-semibold text-gray-700">Filter Tahun:</label>
                <select name="year" onchange="this.form.submit()" class="border-gray-300 rounded focus:outline-[#FF9966] text-sm">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Grafik Status Murid (Jan-Des) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Status Pendaftaran Murid (Jan - Des)</h3>
                <canvas id="studentStatusChart"></canvas>
            </div>

            <!-- Grafik Status Pembayaran (Jan-Des) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Status Pembayaran (Jan - Des)</h3>
                <canvas id="paymentStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bagian Daftar Murid -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Daftar Murid Terdaftar</h3>
        
        <!-- Search Form -->
        <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
            <input type="hidden" name="year" value="{{ $filterYear }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / nama panggilan..."
                class="w-full md:w-1/3 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-xl hover:bg-orange-600 transition text-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('dashboard.unit.info.detail', $unit->id) }}?year={{ $filterYear }}" class="bg-gray-400 text-white px-4 py-2 rounded-xl hover:bg-gray-500 transition text-sm">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Nama Panggilan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($heads as $index => $head)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border">{{ $heads->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border">{{ $head->murid->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border">{{ $head->murid->nama_panggilan ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border">{{ $head->programs->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border">{{ $head->class->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center border">
                            @if($head->status == 'Aktif')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $head->status }}</span>
                            @elseif($head->status == 'Lulus')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $head->status }}</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $head->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 border">Data murid tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $heads->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart Status Murid
        const ctxStudentStatus = document.getElementById('studentStatusChart').getContext('2d');
        new Chart(ctxStudentStatus, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    { label: 'Aktif', data: {!! json_encode($statusMuridData['Aktif']) !!}, backgroundColor: '#10b981' },
                    { label: 'Lulus', data: {!! json_encode($statusMuridData['Lulus']) !!}, backgroundColor: '#3b82f6' },
                    { label: 'Cuti', data: {!! json_encode($statusMuridData['Cuti']) !!}, backgroundColor: '#f59e0b' },
                    { label: 'Keluar', data: {!! json_encode($statusMuridData['Keluar']) !!}, backgroundColor: '#ef4444' },
                    { label: 'Pindah', data: {!! json_encode($statusMuridData['Pindah']) !!}, backgroundColor: '#8b5cf6' }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: false },
                    y: { stacked: false, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Chart Status Pembayaran
        const ctxPaymentStatus = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(ctxPaymentStatus, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    { label: 'Lunas', data: {!! json_encode($statusBayarData['Lunas']) !!}, backgroundColor: '#10b981' },
                    { label: 'Tagihan', data: {!! json_encode($statusBayarData['Tagihan']) !!}, backgroundColor: '#ef4444' },
                    { label: 'Menunggu', data: {!! json_encode($statusBayarData['Menunggu']) !!}, backgroundColor: '#f59e0b' }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: false },
                    y: { stacked: false, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endsection
