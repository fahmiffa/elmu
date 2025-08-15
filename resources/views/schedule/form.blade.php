@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        @isset($items)
            <form method="POST" action="{{ route('dashboard.jadwal.update', $items->id) }}">
                @method('PUT')
            @else
                <form method="POST" action="{{ route('dashboard.jadwal.store') }}">
                @endisset
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Guru</label>
                        <select name="guru"
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Guru</option>
                            @foreach ($guru as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                        @error('guru')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Murid</label>
                        <select name="murid"
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Murid</option>
                            @foreach ($murid as $row)
                                <option value="{{ $row->id }}">{{ $row->murid->name }}</option>
                            @endforeach
                        </select>
                        @error('murid')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4" x-data="jadwal()">
                        <template x-for="(pertemuan, index) in pertemuanList" :key="index">
                            <div class="border border-gray-300 p-4 mb-4 rounded-xl shadow">
                                <div class="mb-2">
                                    <label class="block text-sm font-medium">Nama Pertemuan</label>
                                    <div class="flex gap-2">
                                        <input type="text"  :name="`pertemuan[${index}][nama]`" x-model="pertemuan.nama"
                                            class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                                            :placeholder="`Pertemuan ${index+1}`">
                                        <button type="button" @click="pertemuanList.splice(index, 1)"
                                            class="text-red-600 text-sm hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-trash2-icon lucide-trash-2">
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <template x-for="(item, i) in pertemuan.tanggalList" :key="i">
                                    <div class="flex flex-col gap-1 mb-4">
                                        <div class="flex items-center gap-2">
                                            <input type="datetime-local" :name="`pertemuan[${index}][tanggal][]`"x-model="item.tanggal"
                                                class="border border-gray-300 ring-0 rounded-xl p-2 w-50 focus:outline-[#FF9966]" />
                                            <button type="button" @click="pertemuan.tanggalList.splice(i, 1)"
                                                class="text-red-500 hover:text-red-700 text-xs md:text-sm">
                                                <!-- icon trash -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-trash2-icon lucide-trash-2">
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                    <path d="M3 6h18" />
                                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-xs text-gray-500 italic" x-text="formatWIB(item.tanggal)"></div>

                                        {{-- <input type="text" x-model="item.materi" placeholder="Materi pertemuan"
                                            class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" /> --}}
                                    </div>
                                </template>


                                <button type="button" @click="addTanggal(index)"
                                    class="text-orange-600 text-sm mt-2 hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-calendar-plus-icon lucide-calendar-plus">
                                        <path d="M16 19h6" />
                                        <path d="M16 2v4" />
                                        <path d="M19 16v6" />
                                        <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5" />
                                        <path d="M3 10h18" />
                                        <path d="M8 2v4" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addPertemuan()"
                            class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-file-plus2-icon lucide-file-plus-2">
                                <path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4" />
                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                <path d="M3 15h6" />
                                <path d="M6 12v6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <button type="submit"
                        class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
                </div>
            </form>
    </div>
@endsection
