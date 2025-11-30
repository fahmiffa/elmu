@extends('base.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="videoForm('{{ old('role', $items->role ?? '') }}')">

            <!-- Notifikasi sukses -->
            <div x-show="success" x-transition class="mb-4 p-3 rounded bg-green-500 text-white text-sm">
                Berhasil ditambahkan! Mengalihkan...
            </div>



            <div class="font-semibold mb-3 text-xl">{{ $action }}</div>

            <form method="POST"
                action="{{ isset($items) ? route('dashboard.video.update', $items->id) : route('dashboard.video.store') }}"
                enctype="multipart/form-data" class="flex flex-col" @submit.prevent="upload">

                @csrf
                @isset($items)
                    @method('PUT')
                @endisset

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                    {{-- Nama --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $items->name ?? '') }}"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">

                        @error('name')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload Video --}}
                    <div class="mb-4">
                        <label class="block mb-2 font-semibold">Video :</label>

                        <input type="file" name="video" accept="video/mp4" required @change="validateFile"
                            class="border border-gray-300 px-3 py-2 rounded cursor-pointer focus:ring-2 focus:ring-[#FF9966]">

                        {{-- Progress Bar --}}
                        <div x-show="uploading" class="mt-2 w-full bg-gray-200 rounded overflow-hidden">
                            <div class="bg-indigo-600 text-white text-xs text-center py-1" :style="`width: ${progress}%`"
                                x-text="progress + '%'">
                            </div>
                        </div>

                        {{-- Error --}}
                        <p x-show="error" class="text-red-600 text-sm mt-1" x-text="error"></p>

                        @error('video')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilihan Role --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Status</label>

                        <div class="flex items-center">
                            <label class="mr-4">
                                <input type="radio" name="role" value="2" x-model="role">
                                Murid
                            </label>

                            <label>
                                <input type="radio" name="role" value="3" x-model="role">
                                Guru
                            </label>
                        </div>

                        @error('role')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih murid --}}
                    <div class="mb-4" x-show="role === '2'">
                        <label>Pilih Murid</label>
                        <select name="murid" x-data="dropdownSelect" class="my-3">
                            @foreach ($student as $item)
                                <option value="{{ $item->user }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Error murid --}}
                    @error('murid')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" :disabled="uploading"
                    class="bg-orange-500 text-white text-sm font-bold py-2 px-3 w-25 rounded-2xl hover:bg-orange-700">
                    Simpan
                </button>
            </form>
        </div>
    @endsection
