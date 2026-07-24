@extends('base.layout')
@section('title', 'Dashboard Pendaftaran')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    @php
        $isPaginator = $items instanceof \Illuminate\Pagination\AbstractPaginator;
        $collectionItems = $isPaginator ? $items->items() : $items;
    @endphp

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('dashboard.reg.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
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

            @if(request()->anyFilled(['search', 'unit', 'program']))
                <a href="{{ route('dashboard.reg.index') }}" class="text-sm text-orange-600 hover:underline">Reset Filter</a>
            @endif

            <input type="hidden" name="per_page" value="{{ request('per_page', 50) }}" />
        </form>

        <a href="{{ route('dashboard.reg.create') }}"
            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
            Tambah
        </a>
    </div>

    <div class="flex items-center gap-2 mb-3">
        <span class="text-sm">Show:</span>
        <select onchange="window.location.href=this.value"
            class="border border-gray-300 rounded-lg p-2 focus:outline-[#FF9966]">
            @php
                $urlParams = request()->except('per_page');
            @endphp
            <option value="{{ route('dashboard.reg.index', array_merge($urlParams, ['per_page' => 10])) }}" {{ request('per_page', '50') == '10' ? 'selected' : '' }}>10</option>
            <option value="{{ route('dashboard.reg.index', array_merge($urlParams, ['per_page' => 50])) }}" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
            <option value="{{ route('dashboard.reg.index', array_merge($urlParams, ['per_page' => 100])) }}" {{ request('per_page', '50') == '100' ? 'selected' : '' }}>100</option>
            <option value="{{ route('dashboard.reg.index', array_merge($urlParams, ['per_page' => 200])) }}" {{ request('per_page', '50') == '200' ? 'selected' : '' }}>200</option>
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
                    <th class="px-4 py-2">Kontrak</th>
                    <th class="px-4 py-2">Program</th>
                    <th class="px-4 py-2 text-nowrap">Tanggal</th>
                    <th class="px-4 py-2 text-center text-nowrap">Aksi</th>
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
                        <td class="px-4 py-2 text-nowrap">{{ $row->induk }}</td>
                        <td class="px-4 py-2">{{ $row->murid->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $row->murid->nama_panggilan ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $row->kontrak->name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <dl>
                                <dt class="font-semibold capitalize">{{ $row->programs->name ?? '-' }}</dt>
                                <dt class="capitalize">{{ $row->class->name ?? '-' }}</dt>
                                <dt class="capitalize">{{ $row->units->name ?? '-' }}</dt>
                            </dl>
                        </td>
                        <td class="px-4 py-2 text-nowrap">{{ $row->waktu }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('dashboard.reg.edit', md5($row->id)) }}"
                                    class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z" />
                                    </svg>
                                </a>
                                <form action="{{ route('dashboard.reg.destroy', md5($row->id)) }}" method="POST"
                                    onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center px-4 py-2 text-gray-500">Tidak ada data ditemukan.</td>
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