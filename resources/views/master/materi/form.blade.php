@extends('base.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="materiForm({})">

        <div x-show="success" x-transition class="mb-4 p-3 rounded bg-green-500 text-white text-sm">
            Berhasil ditambahkan! Mengalihkan...
        </div>
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>

        <form method="POST"
            action="{{ isset($items) ? route('dashboard.master.materi.update', $items->id) : route('dashboard.master.materi.store') }}"
            enctype="multipart/form-data" class="flex flex-col" @submit.prevent="upload">
            @csrf
            @isset($items)
                @method('PUT')
            @endisset

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                {{-- Upload File --}}
                <div class="mb-4">
                    <label class="block mb-2 font-semibold">PDF File :</label>
                    <input type="file" name="materi" accept="application/pdf" required
                        class="border border-gray-300 px-3 py-2 rounded cursor-pointer focus:ring-2 focus:ring-[#FF9966]"
                        @change="validateFile">

                    {{-- Progress Bar --}}
                    <div x-show="uploading" class="mt-2 w-full bg-gray-200 rounded overflow-hidden">
                        <div class="bg-indigo-600 text-white text-xs text-center py-1" :style="`width: ${progress}%`"
                            x-text="progress + '%'">
                        </div>
                    </div>

                    {{-- Error --}}
                    <p x-show="error" class="text-red-600 text-sm mt-1" x-text="error"></p>

                    @error('materi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label>Pilih Program</label>
                    <select name="program" x-data="dropdownSelect" class="my-3">
                        @foreach ($program as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('program')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" :disabled="uploading"
                class="bg-orange-500 text-white font-bold py-2 px-3  w-25 rounded-2xl hover:bg-orange-700 text-sm cursor-pointer">
                Simpan
            </button>
        </form>
    </div>
@endsection
