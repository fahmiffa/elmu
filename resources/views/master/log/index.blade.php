@extends('base.layout')
@section('title', 'Log Aktivitas')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="{ modalOpen: false, selectedItem: null }">

    <div class="mb-4 flex justify-between items-center gap-2">
        <h2 class="text-xl font-bold text-gray-800">Log Aktivitas User</h2>

        <form action="{{ route('dashboard.master.log.clear') }}" method="POST"
            onsubmit="return confirm('Kosongkan semua log?')">
            @csrf
            <button type="submit"
                class="cursor-pointer bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl text-xs">
                Kosongkan Log
            </button>
        </form>
    </div>

    @if (session('status'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Aksi</th>
                    <th class="px-4 py-2">Method</th>
                    <th class="px-4 py-2">IP</th>
                    <th class="px-4 py-2">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $index => $log)
                <tr class="border-t border-gray-300 hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $logs->firstItem() + $index }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-4 py-2 font-semibold">{{ $log->user->name ?? 'System' }}</td>
                    <td class="px-4 py-2">
                        <span
                            class="px-2 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">{{ $log->action }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @php
                        $methodColor = match ($log->method) {
                        'POST' => 'bg-green-100 text-green-700',
                        'PUT', 'PATCH' => 'bg-yellow-100 text-yellow-700',
                        'DELETE' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700',
                        };
                        @endphp
                        <span
                            class="px-2 py-1 rounded-lg text-xs font-bold {{ $methodColor }}">{{ $log->method }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $log->ip }}</td>
                    <td class="px-4 py-2">
                        <button
                            @click="selectedItem = {
                                        url: '{{ $log->url }}',
                                        user_agent: '{{ addslashes($log->user_agent) }}',
                                        payload: {{ json_encode($log->payload) }}
                                    }; modalOpen = true"
                            class="text-blue-500 hover:underline text-xs cursor-pointer">
                            Lihat
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center px-4 py-6 text-gray-500">Belum ada log aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

    <!-- Modal Detail -->
    <div x-show="modalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6" @click.away="modalOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-lg font-bold">Detail Aktivitas</h3>
                <button @click="modalOpen = false" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <p class="text-sm text-gray-500 font-semibold">URL</p>
                    <p class="font-mono text-xs bg-gray-100 p-2 rounded break-all" x-text="selectedItem?.url">
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">User Agent</p>
                    <p class="text-xs italic text-gray-600" x-text="selectedItem?.user_agent"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-semibold">Payload (Data dikirim)</p>
                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs overflow-x-auto"
                        x-text="selectedItem?.payload ? JSON.stringify(selectedItem.payload, null, 2) : 'Tidak ada data'"></pre>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="modalOpen = false"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-xl text-sm font-bold cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection