@extends('base.layout')
@section('title', $action)
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        @if ($errors->any())
            <div class="text-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST"
            action="{{ isset($items) ? route('dashboard.master.jadwal.update', $items->id) : route('dashboard.master.jadwal.store') }}">
            @isset($items)
                @method('PUT')
            @endisset
            @csrf
            <div class="flex-row mb-4" x-data="jadwalForm({{ isset($jadwals) ? $jadwals->toJson() : '' }})">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                    <select name="unit" required
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        @isset($items)
                                <option value="{{ $items->id }}">{{ $items->name }}
                                </option>
                        @else
                            <option value="">Pilih Unit</option>
                            @foreach ($unit as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}
                                </option>
                            @endforeach
                        @endisset

                    </select>
                    @error('unit')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <template x-for="(jadwal, index) in jadwals" :key="index">
                    <div class="flex-row border border-gray-300 p-5 rounded-2xl mb-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="mb-4">
                                @isset($items)
                                    <input type="hidden" :value="jadwal.id" x-model="jadwal.id" :name="`jadwal[${index}][id]`">
                                @endisset

                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                                <div class="relative">
                                    <input type="text" :name="`jadwal[${index}][name]`" x-model="jadwal.name"
                                        value="{{ old('name', $items->name ?? '') }}"
                                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                </div>
                                @error('name')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Hari</label>
                                <select :name="`jadwal[${index}][hari]`" x-model="jadwal.hari"
                                    class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                                    required>
                                    <option value="">Pilih hari</option>
                                    <option value="1">Senin</option>
                                    <option value="2">Selasa</option>
                                    <option value="3">Rabu</option>
                                    <option value="4">Kamis</option>
                                    <option value="5">Jumat</option>
                                    <option value="6">Sabtu</option>
                                    <option value="7">Minggu</option>
                                </select>
                                @error('hari')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4 grid-cols-2" x-data="{
                                startTime: '',
                                endTime: '',
                                formatWIB(timeStr) {
                                    if (!timeStr) return '';
                                    const [hour, minute] = timeStr.split(':');
                                    const date = new Date();
                                    date.setHours(parseInt(hour), parseInt(minute), 0, 0);
                                    return date.toLocaleString('id-ID', {
                                        timeZone: 'Asia/Jakarta',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    }) + ' WIB';
                                }
                            }">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Waktu</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div class="p-1">
                                        <label for="start"
                                            class="block mb-1 text-xs font-semibold text-gray-700">Mulai</label>
                                        <input type="time" x-model="jadwal.start_time"
                                            :name="`jadwal[${index}][start_time]`"
                                            class="border border-gray-300  ring-0 rounded-xl px-2 py-2 w-full focus:outline-[#FF9966]">
                                        <p>
                                            <span class="text-xs text-red-600 px-3"
                                                x-text="formatWIB(jadwal.start_time) || '-'"></span>
                                        </p>

                                        @error('start_time')
                                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="p-1">
                                        <label for="end"
                                            class="block mb-1 text-xs font-semibold text-gray-700">Selesai</label>
                                        <input type="time" x-model="jadwal.end_time" :name="`jadwal[${index}][end_time]`"
                                            class="border border-gray-300  ring-0 rounded-xl px-2 py-2 w-full focus:outline-[#FF9966]">
                                        <p>
                                            <span class="text-xs text-red-600 px-3"
                                                x-text="formatWIB(jadwal.end_time) || '-'"></span>
                                        </p>

                                        @error('end_time')
                                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                            class="cursor-pointer bg-red-500 text-xs hover:bg-red-700 text-white font-bold px-3 py-2 rounded-2xl focus:outline-none focus:shadow-outline"
                            @click="removeJadwal(index)">Hapus</button>
                    </div>
                </template>

                @error('jadwal')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button type="button"
                        class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline"
                        @click="addJadwal()">Tambah</button>
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
