@extends('base.layout')
@section('title', 'Dashboard Level')

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="levelHandler()">
    @php
        $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
        $collectionItems = $isPaginator ? $items->items() : $items;
    @endphp

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <form method="GET" action="{{ route('dashboard.level') }}" class="flex flex-wrap items-center gap-2 w-full">
            <input type="text" name="search" placeholder="Cari Nama" value="{{ request('search') }}"
                class="flex-1 min-w-[200px] border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <select name="unit" onchange="this.form.submit()"
                class="border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Unit</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ request('unit') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <select name="program" onchange="this.form.submit()"
                class="border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]">
                <option value="">Semua Program</option>
                @foreach($pro as $p)
                <option value="{{ $p->id }}" {{ request('program') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>

            @if(request()->anyFilled(['search', 'unit', 'program']))
                <a href="{{ route('dashboard.level') }}" class="text-sm text-orange-600 hover:underline">Reset Filter</a>
            @endif

            <input type="hidden" name="per_page" value="{{ request('per_page', 50) }}" />
        </form>
    </div>

    <div class="flex items-center gap-2 mb-3">
        <span class="text-xs">Show: </span>
        <select onchange="window.location.href=this.value"
            class="border border-gray-300 rounded-lg p-2 focus:outline-[#FF9966]">
            @php
                $urlParams = request()->except('per_page');
            @endphp
            <option value="{{ route('dashboard.level', array_merge($urlParams, ['per_page' => 10])) }}" {{ request('per_page', '50') == '10' ? 'selected' : '' }}>10</option>
            <option value="{{ route('dashboard.level', array_merge($urlParams, ['per_page' => 50])) }}" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
            <option value="{{ route('dashboard.level', array_merge($urlParams, ['per_page' => 100])) }}" {{ request('per_page', '50') == '100' ? 'selected' : '' }}>100</option>
            <option value="{{ route('dashboard.level', array_merge($urlParams, ['per_page' => 200])) }}" {{ request('per_page', '50') == '200' ? 'selected' : '' }}>200</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Panggilan</th>
                    <th class="px-4 py-2">Program</th>
                    <th class="px-4 py-2">Level</th>
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
                        <td class="px-4 py-2">{{ $row->murid->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $row->murid->nama_panggilan ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="whitespace-nowrap">
                                <dl>
                                    <dt class="font-semibold capitalize">{{ $row->units->name ?? '-' }}</dt>
                                    <dt class="text-xs">
                                        <span>{{ $row->programs->name ?? '-' }}</span>
                                        <span>{{ $row->class->name ?? '-' }}</span>
                                    </dt>
                                </dl>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            @forelse($row->level as $item)
                                <div class="flex gap-3 justify-between items-center mb-1">
                                    <div class="font-semibold capitalize">
                                        <span>{{ $item->level }}</span>
                                        <span class="text-xs font-normal text-gray-500">({{ $item->tgl }})</span>
                                    </div>
                                    @if($item->status === 0)
                                        <button onclick="openModal({{ $item->id }}, {{ $item->student_id }}, '{{ addslashes($item->note ?? '') }}')"
                                            class="bg-orange-500 text-xs cursor-pointer font-semibold text-white px-3 py-2 rounded-2xl hover:bg-orange-600"
                                            type="button">Verifikasi</button>
                                    @endif
                                </div>
                            @empty
                                <span class="text-gray-400 text-xs">Tidak ada level</span>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center px-4 py-2 text-gray-500">Tidak ada data ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Modal Upgrade Level -->
        <div id="levelModal" x-cloak style="display:none; background-color: rgba(0,0,0,0.5);"
            class="fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
                <h2 class="text-sm font-bold mb-4">Upgrade Level</h2>
                <form id="levelForm" method="POST">
                    @csrf
                    <input type="hidden" name="level" id="levelId">

                    <div class="text-red-500 text-xs font-semibold mb-3">Silahkan Ceklist, Jika siswa/murid sudah menyelesaikan program belajar</div>
                    <div class="mb-3 flex items-center gap-2">
                        <input type="checkbox" name="done" id="selesai" class="h-4 w-4" onchange="toggleBook(this)">
                        <label for="selesai" class="text-sm">Selesai</label>
                    </div>

                    <div class="mb-4">
                        <select name="book" id="bookSelect" required
                            class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Buku</option>
                            @foreach ($lay as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none">
                        Upgrade
                    </button>
                </form>
                <button onclick="closeModal()"
                    class="mt-6 bg-gray-800 text-white text-xs cursor-pointer px-3 py-2 rounded-2xl hover:bg-gray-900 float-end"
                    type="button">Close</button>
            </div>
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
</div>

<script>
function openModal(levelId, studentId, note) {
    document.getElementById('levelModal').style.display = 'flex';
    document.getElementById('levelForm').action = '/dashboard/layanan/' + studentId;
    document.getElementById('levelId').value = levelId;
}

function closeModal() {
    document.getElementById('levelModal').style.display = 'none';
}

function toggleBook(checkbox) {
    document.getElementById('bookSelect').required = !checkbox.checked;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endsection