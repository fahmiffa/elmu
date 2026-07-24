@extends('base.layout')
@section('title', 'Dashboard Akademik')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    @php
        $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
        $collectionItems = $isPaginator ? $items->items() : $items;
    @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('dashboard.akademik') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" placeholder="Cari Nama / Panggilan" value="{{ request('search') }}"
                class="w-full md:w-1/3 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <select name="unit" onchange="this.form.submit()"
                class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <select name="program" onchange="this.form.submit()"
                class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Program</option>
                @foreach($pro as $p)
                <option value="{{ $p->id }}" {{ request('program') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()"
                class="w-full md:w-auto border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Status</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Aktif</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Lulus</option>
                <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Cuti</option>
                <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Keluar</option>
                <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Pindah</option>
            </select>

            @if(request()->anyFilled(['search', 'unit', 'program', 'status']))
                <a href="{{ route('dashboard.akademik') }}" class="text-sm text-orange-600 hover:underline">Reset Filter</a>
            @endif

            <input type="hidden" name="per_page" value="{{ request('per_page', 50) }}" />
        </form>
    </div>

    <div class="flex items-center gap-2 mb-3">
        <span class="text-sm">Show:</span>
        <select onchange="window.location.href=this.value"
            class="border border-gray-300 rounded-lg p-2 focus:outline-[#FF9966]">
            @php
                $urlParams = request()->except('per_page');
            @endphp
            <option value="{{ route('dashboard.akademik', array_merge($urlParams, ['per_page' => 10])) }}" {{ request('per_page', '50') == '10' ? 'selected' : '' }}>10</option>
            <option value="{{ route('dashboard.akademik', array_merge($urlParams, ['per_page' => 50])) }}" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
            <option value="{{ route('dashboard.akademik', array_merge($urlParams, ['per_page' => 100])) }}" {{ request('per_page', '50') == '100' ? 'selected' : '' }}>100</option>
            <option value="{{ route('dashboard.akademik', array_merge($urlParams, ['per_page' => 200])) }}" {{ request('per_page', '50') == '200' ? 'selected' : '' }}>200</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2 text-nowrap">Nomor Induk</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Panggilan</th>
                    <th class="px-4 py-2">Program</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collectionItems as $index => $row)
                    <tr class="border-t border-gray-300">
                        <td class="px-4 py-2">
                            @if($isPaginator)
                                {{ $items->firstItem() + $index }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $row->induk }}</td>
                        <td class="px-4 py-2">{{ $row->murid->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $row->murid->nama_panggilan ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <dl>
                                <dt class="font-semibold capitalize">{{ $row->programs->name ?? '-' }}</dt>
                                <dt class="capitalize">{{ $row->class->name ?? '-' }}</dt>
                                <dt class="capitalize">{{ $row->units->name ?? '-' }}</dt>
                            </dl>
                        </td>
                        <td class="px-4 py-2">
                            @php
                                $statusColors = [
                                    0 => 'bg-orange-500',
                                    1 => 'bg-green-500',
                                    2 => 'bg-red-500',
                                    3 => 'bg-gray-800',
                                    4 => 'bg-purple-500',
                                ];
                                $color = $statusColors[$row->done] ?? 'bg-gray-500';
                            @endphp
                            <button onclick="openStatusModal({{ $row->id }}, '{{ $row->status }}', '{{ addslashes($row->note ?? '') }}')"
                                class="{{ $color }} text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:opacity-80"
                                type="button">{{ $row->status }}</button>
                            <div class="text-xs text-gray-500 mt-1">{{ $row->note }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('dashboard.akademik.detail', md5($row->murid->user ?? '')) }}"
                                    class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors" title="Detail Siswa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center px-4 py-2 text-gray-500">Tidak ada data ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isPaginator)
    <div class="flex justify-between items-center mt-4">
        <div>
            {{ $items->links() }}
        </div>
        <div class="text-sm text-gray-600">
            Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
        </div>
    </div>
    @endif
</div>

<!-- Status Modal -->
<div id="statusModal" style="display:none; background-color: rgba(0,0,0,0.5);"
    class="fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
        <h2 class="text-sm font-bold mb-4">Update Status</h2>
        <form id="statusForm" method="POST">
            @csrf
            <input type="hidden" name="id" id="statusId">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                <select class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" name="status" required>
                    <option value="">Pilih</option>
                    <option value="3">Off (Keluar)</option>
                    <option value="2">Cuti</option>
                    <option value="1">Lulus</option>
                    <option value="0">Aktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Keterangan</label>
                <textarea name="keterangan" id="statusNote" class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"></textarea>
            </div>
            <button type="submit"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Simpan
            </button>
        </form>
        <button onclick="closeStatusModal()"
            class="mt-6 bg-gray-800 text-white text-xs cursor-pointer px-3 py-2 rounded-2xl hover:bg-gray-900 float-end"
            type="button">Close</button>
    </div>
</div>

<script>
function openStatusModal(id, status, note) {
    document.getElementById('statusModal').style.display = 'flex';
    document.getElementById('statusForm').action = '/dashboard/status/' + id;
    document.getElementById('statusId').value = id;
    document.getElementById('statusNote').value = note;
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeStatusModal();
});
</script>
@endsection