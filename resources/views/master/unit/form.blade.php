@extends('base.layout')
@section('title', $action)
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="formHandler('{{ route('dashboard.master.unit.index') }}')">
    <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
    @isset($items)
    <form method="POST" action="{{ route('dashboard.master.unit.update', $items->id) }}" @submit.prevent="submit">
        @method('PUT')
        @else
        <form method="POST" action="{{ route('dashboard.master.unit.store') }}" @submit.prevent="submit">
            @endisset
            @csrf
            <div class="grid grid-cols-2 gap-2">
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
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                    <textarea name="addr" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('addr', $items->addr ?? '') }}</textarea>
                    @error('addr')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                    <select name="kelas[]" required x-data="dropdownSelect()"
                        multiple>
                        <option value="">Pilih Kelas</option>
                        @php

                        @endphp
                        @foreach ($kelas as $row)
                        <option value="{{ $row->id }}" @selected(in_array($row->id,isset($items) ? $items->kelas->pluck('id')->toArray() : []))>{{ $row->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('kelas')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex items-center">
                <button type="submit" :disabled="loading"
                    class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading">Mohon Tunggu...</span>
                </button>
            </div>
        </form>
</div>
@endsection