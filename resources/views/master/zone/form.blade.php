@extends('base.layout')
@section('title', $action)
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        @isset($items)
            <form method="POST" action="{{ route('dashboard.master.zone.update', $items->id) }}">
                @method('PUT')
            @else
                <form method="POST" action="{{ route('dashboard.master.zone.store') }}">
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
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP</label>
                        <input type="number" name="hp" value="{{ old('hp', $items->hp ?? '') }}"
                            class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        @error('hp')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Leader</label>
                        <div class="relative">
                            <input type="text" name="pic" value="{{ old('pic', $items->pic ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('pic')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                        <select name="unit[]" x-data="dropdownSelect()"
                             multiple>
                            <option value="">Pilih Unit</option>
                            @php

                            @endphp
                            @foreach ($unit as $row)
                                <option value="{{ $row->id }}" @selected(in_array($row->id,isset($items) ? $items->unit->pluck('id')->toArray() : []))>{{ $row->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
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
