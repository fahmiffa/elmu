@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        <form method="POST"
            action="{{ isset($items) ? route('dashboard.master.layanan.update', ['layanan' => $items->id]) : route('dashboard.master.layanan.store') }}"
            enctype="multipart/form-data">
            @php
                $initial = [];
            @endphp
            @isset($items)
                @method('PUT')
                @php
                    $initial = [
                        'kelas_id' => old('kelas', $items->price->kelas ?? ''),
                        'program_id' => old('program', $items->program ?? ''),
                    ];
                @endphp
            @endisset
            @csrf

            <div class="grid grid-cols-2 gap-2" x-data='reg(kelasData, @json($initial))'>
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
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                    <select x-model="selectedKelas" name="kelas"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        <template x-for="(option, index) in optionsKelas" :key="index">
                            <option :value="option.value" x-text="option.label"></option>
                        </template>
                    </select>
                    @error('kelas')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Program Belajar</label>
                    <select x-model="selectedProgram" name="program"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                        required>
                        <template x-for="option in filteredPrograms" :key="option.value">
                            <option :value="option.value" x-text="option.label"></option>
                        </template>
                    </select>
                    @error('program')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4" x-data="currencyInput('{{ old('nominal', isset($items) ? $items->price->harga : '') }}')">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Harga</label>
                    <div class="relative">
                        <input type="text" x-model="display" @input="formatInput"
                            class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full md:w-1/2 focus:outline-[#FF9966]">
                        <input type="hidden" name="price" :value="raw">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div></div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi</label>
                    <textarea name="des" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('des', $items->des ?? '') }}</textarea>
                    @error('des')
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
@push('script')
    <script>
        window.kelasData = @json($kelas);
    </script>
@endpush
