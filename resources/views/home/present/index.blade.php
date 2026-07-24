@extends('base.layout')
@section('title', 'Dashboard Absensi')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    @php
        $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
        $collectionItems = $isPaginator ? $items->items() : $items;
    @endphp

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <form method="GET" action="{{ route('dashboard.absensi') }}" class="flex flex-wrap items-center gap-2 w-full">
            <input type="text" name="search" placeholder="Cari Nama / Panggilan" value="{{ request('search') }}"
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
                <a href="{{ route('dashboard.absensi') }}" class="text-sm text-orange-600 hover:underline">Reset Filter</a>
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
            <option value="{{ route('dashboard.absensi', array_merge($urlParams, ['per_page' => 10])) }}" {{ request('per_page', '50') == '10' ? 'selected' : '' }}>10</option>
            <option value="{{ route('dashboard.absensi', array_merge($urlParams, ['per_page' => 50])) }}" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
            <option value="{{ route('dashboard.absensi', array_merge($urlParams, ['per_page' => 100])) }}" {{ request('per_page', '50') == '100' ? 'selected' : '' }}>100</option>
            <option value="{{ route('dashboard.absensi', array_merge($urlParams, ['per_page' => 200])) }}" {{ request('per_page', '50') == '200' ? 'selected' : '' }}>200</option>
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
                    <th class="px-4 py-2">Waktu</th>
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
                            @forelse($row->present as $item)
                                <dl class="mb-3 last:mb-0 border-b border-gray-100 pb-2 last:border-0">
                                    <dt class="font-semibold capitalize">{{ $item->tanggal }}</dt>
                                    <div class="flex items-center gap-2">
                                        <dd class="text-xs text-gray-700 font-medium">Guru: {{ $item->guru->name ?? '-' }}</dd>
                                        @if($item->program)
                                            <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-md font-bold">{{ $item->program->name }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 space-y-0.5">
                                        @if($item->hal)
                                            <dd class="text-[11px] text-blue-600 flex items-center gap-1">
                                                <span class="font-bold">Hal:</span> <span>{{ $item->hal }}</span>
                                            </dd>
                                        @endif
                                        @if($item->Materi)
                                            <dd class="text-[11px] text-green-600 flex items-center gap-1">
                                                <span class="font-bold">Materi:</span> <span>{{ $item->Materi }}</span>
                                            </dd>
                                        @endif
                                        @if($item->Keterangan)
                                            <dd class="text-[11px] text-gray-500 italic flex items-start gap-1">
                                                <span class="font-bold not-italic">Ket:</span> <span>{{ $item->Keterangan }}</span>
                                            </dd>
                                        @endif
                                    </div>
                                </dl>
                            @empty
                                <span class="text-gray-400 text-xs">Tidak ada absensi</span>
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
@endsection