@extends('base.layout')
@section('title', $action)
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="formHandler('{{ route('dashboard.master.program.index') }}')">
    <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
    <form method="POST"
        action="{{ isset($items) ? route('dashboard.master.program.update', $items->id) : route('dashboard.master.program.store') }}"
        class="flex flex-col" @submit.prevent="submit">
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
                <label class="block text-gray-700 text-sm font-semibold mb-2">Kode</label>
                <div class="relative">
                    <input type="text" name="kode" value="{{ old('kode', $items->kode ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
                @error('kode')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Level</label>
                <input type="number" name="level" value="{{ old('level', $items->level ?? '') }}"
                    class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                @error('level')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4 col-span-2">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
                <textarea name="des" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('des', $items->des ?? '') }}</textarea>
                @error('des')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4 col-span-2" x-data="paket({{ json_encode(isset($data) ? $data : []) }})">
                <div class="flex items-end space-x-3">
                    <div class="w-1/3">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                        <select name="kelas" x-model="selectedId" x-ref="kelasSelect"
                            class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelas as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" x-on:click="addField()" :disabled="!selectedId"
                        class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                        Tambah
                    </button>
                </div>

                <template x-for="(field, index) in fields" :key="index">
                    <div class="w-1/2 mb-3">
                        <label class="text-xs" x-text="'Harga Kelas ' + field.name"></label>
                        <div class="flex items-center space-x-2">
                            <input type="hidden" :value="field.price" :name="'price[' + index + ']'">
                            <input type="hidden" :value="field.id" :name="'id[' + index + ']'">
                            <input type="text" x-model="field.value" :name="'harga[' + index + ']'"
                                x-on:input="formatFieldValue(index)"
                                class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" />

                            <button type="button" x-on:click="removeField(index)"
                                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                    <path d="M3 6h18" />
                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                @error('price')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-4 text-gray-500 text-sm font-bold">STATER KIT</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="mb-4" x-data="currencyInput('{{ old('nominal',isset($items) ? $items->kit : 0)}}')">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nominal</label>
                <div class="relative">
                    <input type="text" x-model="display" @input="formatInput"
                        class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full md:w-1/2 focus:outline-[#FF9966]">
                    <input type="hidden" name="nominal" :value="raw">
                </div>
                @error('nominal')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 col-span-2">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
                <textarea name="kit_des" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('kit_des', $items->kit_des ?? '') }}</textarea>
                @error('kit_des')
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