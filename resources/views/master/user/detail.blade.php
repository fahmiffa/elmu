@extends('base.layout')
@section('title', 'Detail ' . $user->name)
@section('content')
<div class="bg-white rounded-lg shadow-md p-6" x-data="{ activeTab: 'data' }">
    <!-- Header with Photo and Basic Info -->
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b pb-6 mb-6">
        <div class="flex-shrink-0 w-32 h-32 md:w-48 md:h-48 mb-4 md:mb-0">
            <img src="{{ ($user->data && $user->data->img) ? asset('storage/' . $user->data->img) : asset('logo.png') }}"
                onerror="this.onerror=null;this.src='{{ asset('logo.png') }}';"
                class="w-full h-full rounded-xl {{ ($user->data && $user->data->img) ? 'object-cover' : 'object-contain' }} shadow-md border bg-gray-50"
                alt="{{ $user->name }}">
        </div>
        <div class="flex-grow text-center md:text-left">
            <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
            <p class="text-gray-500 mb-2">{{ $user->email }} | {{ $user->nomor }}</p>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mt-3">
                <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold uppercase">{{ $user->roles }}</span>
                <span class="px-3 py-1 {{ $user->status == 1 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full text-xs font-bold uppercase">
                    {{ $user->status == 1 ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto scroll-hidden">
        <button @click="activeTab = 'data'" :class="activeTab === 'data' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-orange-500'"
            class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
            Data Profil
        </button>
        <button @click="activeTab = 'level'" :class="activeTab === 'level' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-orange-500'"
            class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
            Level
        </button>
        <button @click="activeTab = 'pembayaran'" :class="activeTab === 'pembayaran' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-orange-500'"
            class="px-6 py-3 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
            Pembayaran
        </button>
    </div>

    <!-- Tabs Content -->
    <div>
        <!-- TAB DATA -->
        <div x-show="activeTab === 'data'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <!-- Data Murid -->
            <div class="mb-8">
                <h3 class="flex items-center text-lg font-bold text-gray-700 mb-4">
                    <span class="bg-orange-500 w-1 h-6 mr-3 rounded-full"></span>
                    Informasi Murid
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Alamat</p>
                        <p class="text-sm text-gray-700">{{ $user->data->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Tempat, Tgl Lahir</p>
                        <p class="text-sm text-gray-700">{{ $user->data->place ?? '-' }}, {{ $user->data->birth ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Agama</p>
                        <p class="text-sm text-gray-700">{{ $user->data->agama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Jenis Kelamin</p>
                        <p class="text-sm text-gray-700">{{ $user->data->gender == 1 ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Sekolah / Kelas</p>
                        <p class="text-sm text-gray-700">{{ $user->data->sekolah_kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">HP Siswa</p>
                        <p class="text-sm text-gray-700">{{ $user->data->hp_siswa ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="mb-8">
                <h3 class="flex items-center text-lg font-bold text-gray-700 mb-4">
                    <span class="bg-blue-500 w-1 h-6 mr-3 rounded-full"></span>
                    Informasi Orang Tua
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Nama Ayah</p>
                        <p class="text-sm text-gray-700">{{ $user->data->dad ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Pekerjaan Ayah</p>
                        <p class="text-sm text-gray-700">{{ $user->data->dadJob ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Nama Ibu</p>
                        <p class="text-sm text-gray-700">{{ $user->data->mom ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Pekerjaan Ibu</p>
                        <p class="text-sm text-gray-700">{{ $user->data->momJob ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">HP Orang Tua</p>
                        <p class="text-sm text-gray-700">{{ $user->data->hp_parent ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div>
                <h3 class="flex items-center text-lg font-bold text-gray-700 mb-4">
                    <span class="bg-green-500 w-1 h-6 mr-3 rounded-full"></span>
                    Informasi Tambahan
                </h3>
                <div class="grid grid-cols-1 gap-6 bg-gray-50 p-6 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Cita-cita</p>
                            <p class="text-sm text-gray-700">{{ $user->data->dream ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Pendidikan Formal</p>
                            <p class="text-sm text-gray-700">{{ $user->data->study ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Pendidikan Non-Formal</p>
                        <div class="text-sm text-gray-700 prose prose-sm max-w-none">{!! $user->data->pendidikan_non_formal ?? '-' !!}</div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Prestasi (3 Tahun Terakhir)</p>
                        <div class="text-sm text-gray-700 prose prose-sm max-w-none">{!! $user->data->prestasi ?? '-' !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB LEVEL -->
        <div x-show="activeTab === 'level'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            @if($user->data && $user->data->reg->count() > 0)
            @foreach($user->data->reg as $reg)
            <div class="mb-6 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-orange-500 px-6 py-3 flex justify-between items-center">
                    <h4 class="text-white font-bold">{{ $reg->programs->name }} - {{ $reg->units->name }}</h4>
                    <span class="px-2 py-1 bg-white/20 text-white rounded text-xs font-mono">{{ $reg->induk }}</span>
                </div>
                <div class="p-6">
                    @if($reg->level->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($reg->level as $level)
                        <div class="bg-white p-4 rounded-lg border flex items-center gap-4 shadow-sm">
                            <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $level->name }}</p>
                                <p class="text-xs text-gray-500">{{ $level->status == 1 ? 'Selesai' : 'Sedang Dipelajari' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-gray-400 italic">Belum ada data level untuk program ini.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <div class="bg-gray-50 rounded-xl p-10 text-center border-2 border-dashed">
                <p class="text-gray-400 font-medium">Siswa belum memiliki pendaftaran aktif.</p>
            </div>
            @endif
        </div>

        <!-- TAB PEMBAYARAN -->
        <div x-show="activeTab === 'pembayaran'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            @if($user->data && $user->data->reg->count() > 0)
            <!-- Loop Registrations -->
            @foreach($user->data->reg as $reg)
            <div class="mb-10">
                <h4 class="text-lg font-bold text-orange-600 mb-4 pb-2 border-b border-orange-100">
                    {{ $reg->programs->name }} ({{ $reg->units->name }})
                </h4>

                <!-- Riwayat Pembayaran Bulanan -->
                <div class="mb-6">
                    <h5 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Riwayat SPP / Bulanan
                    </h5>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Metode</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($reg->bill as $bill)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                        {{ $bill->bulan }}/{{ $bill->tahun }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $bill->first == 1 ? 'Pendaftaran + KIT' : 'SPP Bulanan' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                        Rp {{ number_format($bill->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 capitalize">
                                        {{ $bill->via ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($bill->status == 1)
                                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-bold uppercase">Lunas</span>
                                        @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded text-xs font-bold uppercase">Tagihan</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data pembayaran bulanan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Pembayaran Layanan/Addon -->
                <div>
                    <h5 class="text-sm font-bold text-gray-600 mb-3 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Layanan / Produk Tambahan
                    </h5>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($reg->lay as $lay)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                                        {{ $lay->product->item->name ?? 'Produk' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                        Rp {{ number_format($lay->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $lay->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($lay->status == 1)
                                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs font-bold uppercase">Lunas</span>
                                        @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded text-xs font-bold uppercase">Proses</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data pembelian layanan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="bg-gray-50 rounded-xl p-10 text-center border-2 border-dashed">
                <p class="text-gray-400 font-medium">Siswa belum memiliki riwayat pembayaran.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection