@extends('base.layout')
@section('title', 'Detail Guru')
@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Detail Guru</h2>
            <a href="{{ route('dashboard.master.teach.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm transition duration-200">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Image Section -->
            <div class="flex flex-col items-center p-6 border rounded-xl shadow-sm bg-gray-50">
                <div class="w-48 h-48 rounded-full overflow-hidden mb-4 border-4 border-orange-200">
                    @if($teach->img)
                        <img src="{{ asset('storage/' . $teach->img) }}" alt="{{ $teach->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-gray-800 text-center">{{ $teach->name }}</h3>
                <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold mt-2">{{ $teach->unit->name ?? 'Belum ada unit' }}</span>
            </div>

            <!-- Detail Information -->
            <div class="md:col-span-2 flex flex-col gap-4 p-6 border rounded-xl shadow-sm">
                
                <h4 class="text-lg font-bold text-gray-700 border-b pb-2 mb-2">Informasi Pribadi</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nomor HP</p>
                        <p class="font-semibold text-gray-800">{{ $teach->hp }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Lahir</p>
                        <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($teach->birth)->format('d F Y') }} <span class="text-sm font-normal text-gray-500">({{ $teach->age }})</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pendidikan Terakhir</p>
                        <p class="font-semibold text-gray-800">{{ $teach->study }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-semibold text-gray-800">{{ $teach->addr }}</p>
                    </div>
                </div>

                <h4 class="text-lg font-bold text-gray-700 border-b pb-2 mt-4 mb-2">Informasi Akun</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Email Login</p>
                        <p class="font-semibold text-gray-800">{{ $teach->akun->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Hak Akses</p>
                        <p class="font-semibold text-gray-800">{{ $teach->akun ? 'Guru' : '-' }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
