@extends('base.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="{
        error: '',
        validateAndSubmit() {
            this.error = '';
            const fileInput = this.$refs.pdf;
            const file = fileInput.files[0];
    
            if (!file) {
                this.error = 'File belum dipilih.';
                return;
            }
    
            if (file.size > 2 * 1024 * 1024) {
                this.error = 'File maksimal 2MB.';
                return;
            }
    
            const allowedExtensions = ['pdf'];
            const fileExtension = file.name.split('.').pop()?.toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                this.error = 'Format file harus PDF.';
                return;
            }
    
            // Jika valid, submit form
            this.$refs.uploadForm.submit();
        }
    }">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>

        <form x-ref="uploadForm" method="POST"
            action="{{ isset($items) ? route('dashboard.raport.update', ['raport' => $items->id]) : route('dashboard.raport.store') }}"
            enctype="multipart/form-data" class="flex flex-col" @submit.prevent="validateAndSubmit">
            @isset($items)
                @method('PUT')
            @endisset
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nama --}}
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $items->name ?? '') }}"
                        class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- File PDF --}}
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="pdf">File (PDF maks.
                        2MB)</label>
                    <input x-ref="pdf" id="pdf" type="file" name="pdf" accept="application/pdf" required
                        class="border border-gray-300 rounded px-3 py-2 w-full cursor-pointer focus:outline-[#FF9966]">
                    @error('pdf')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <template x-if="error">
                        <p class="text-red-600 text-sm mt-1" x-text="error"></p>
                    </template>
                </div>

                {{-- Pilih Murid --}}
                <div class="md:col-span-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-2" for="murid">Pilih Murid</label>
                    <select name="murid" id="murid"
                        class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        @foreach ($student as $item)
                            <option value="{{ $item->user }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('murid')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Submit Button --}}
            <div class="mt-4">
                <button type="submit"
                    class="bg-orange-500 text-white text-sm font-bold py-2 px-4 rounded-2xl hover:bg-orange-700 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
