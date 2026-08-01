@extends('base.layout')
@section('title', 'Data Permit')
@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Data Permit</h2>
        <a href="{{ route('dashboard.presensi.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
            Tambah Permit
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                    <th class="py-3 px-3 text-left">No</th>
                    <th class="py-3 px-3 text-left">Siswa</th>
                    <th class="py-3 px-3 text-left">Tgl Asal</th>
                    <th class="py-3 px-3 text-left">Sesi Asal</th>
                    <th class="py-3 px-3 text-left">Tgl Pengganti</th>
                    <th class="py-3 px-3 text-left">Sesi Tujuan</th>
                    <th class="py-3 px-3 text-left">Alasan</th>
                    <th class="py-3 px-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 font-light">
                @foreach ($permits as $item)
                @php
                    $sesAsal   = $item->scheduleStudent?->sch?->first() ?? null;
                    $sesTujuan = $item->unitSchedule ?? null;
                @endphp
                <tr class="border-b border-gray-200 hover:bg-orange-50 transition-colors">
                    <td class="py-3 px-3">{{ $loop->iteration }}</td>
                    <td class="py-3 px-3 font-semibold">{{ $item->student->name ?? '-' }}</td>
                    {{-- Tanggal Asal --}}
                    <td class="py-3 px-3">
                        <span class="block text-xs text-orange-500 font-medium">{{ $sesAsal->parse ?? '-' }}</span>
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                    </td>
                    {{-- Sesi Asal --}}
                    <td class="py-3 px-3">
                        @if($sesAsal)
                            <span class="inline-block bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded font-medium">{{ $sesAsal->name }}</span>
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $sesAsal->start_time }} – {{ $sesAsal->end_time }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    {{-- Tanggal Pengganti --}}
                    <td class="py-3 px-3">
                        @if($item->new_date)
                            <span class="block text-xs text-blue-500 font-medium">{{ $sesTujuan->parse ?? '-' }}</span>
                            {{ \Carbon\Carbon::parse($item->new_date)->format('d/m/Y') }}
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    {{-- Sesi Tujuan --}}
                    <td class="py-3 px-3">
                        @if($sesTujuan)
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-medium">{{ $sesTujuan->name }}</span>
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $sesTujuan->start_time }} – {{ $sesTujuan->end_time }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-gray-600">{{ Str::limit($item->why, 40) }}</td>
                    <td class="py-3 px-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="{{ route('dashboard.presensi.edit', $item->id) }}" class="text-blue-500 hover:text-blue-700 font-medium">Edit</a>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('dashboard.presensi.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($permits->isEmpty())
                <tr>
                    <td colspan="8" class="py-6 px-3 text-center text-gray-400">Belum ada data ganti jadwal.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@push('script')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data presensi ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
