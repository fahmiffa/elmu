@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        <form method="POST"
            action="{{ isset($items) ? route('dashboard.video.update', ['video' => $items->id]) : route('dashboard.video.store') }}"
            class="flex flex-col" enctype="multipart/form-data" x-data="{ role: '{{ old('role', $items->role ?? '') }}' }">
            @isset($items)
                @method('PUT')
            @endisset
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name', $items->name ?? '') }}"
                            class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-2 font-semibold" for="video">Video :</label>
                    <input type="file" id="video" name="video" accept="video/*" required
                        class="border border-gray-300  ring-0  px-3 py-2 focus:outline-[#FF9966] rounded cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FF9966]"
                        x-on:change="$el.form.dispatchEvent(new Event('submit', { cancelable: true }))">

                    @error('video')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Status</label>
                    <div class="flex items-center">
                        <input type="radio" id="murid" name="role" value="2" x-model="role" class="mr-2">
                        <label for="murid" class="mr-4">Murid</label>
                        <input type="radio" id="guru" name="role" value="3" x-model="role" class="mr-2">
                        <label for="guru">Guru</label>
                    </div>
                    @error('role')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4" x-show="role === '2'">
                    <label for="murid">Pilih Murid</label>
                    <select x-data="dropdownSelect" name="murid" class="my-3">
                        @foreach ($student as $item)
                            <option value="{{ $item->user }}">{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('murid')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
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
