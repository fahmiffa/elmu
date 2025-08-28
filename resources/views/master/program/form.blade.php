@extends('base.layout')
@section('title', $action)
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        @isset($items)
            <form method="POST" action="{{ route('dashboard.master.program.update', $items->id) }}" class="flex flex-col">
                @method('PUT')
            @else
                <form method="POST" action="{{ route('dashboard.master.program.store') }}" class="flex flex-col">
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
                    <div class="mb-4 col-span-2" x-data="{
                        kelas: '',
                        fields: {{ json_encode($data ?? []) }},
                        addField() {
                            if (!this.selectedId) return;
                            if (this.fields.find(f => f.id == this.selectedId)) {
                                return;
                            }
                            this.fields.push({ id: this.selectedId, value: '', name: this.getSelectedText() });
                        },
                        removeField(index) {
                            this.fields.splice(index, 1);
                        },
                        selectedId: '',
                        getSelectedText() {
                            const select = this.$refs.kelasSelect;
                            return select.options[select.selectedIndex].text;
                        }
                    }">
                        <div class="flex items-end space-x-3">
                            <div class="w-1/3">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                                <select name="kelas" x-model="selectedId" x-ref="kelasSelect"
                                    class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
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
                        <template x-for="(field, index) in fields" :key="index"
                            class="flex items-center space-x-2 mb-3">
                            <div class="w-1/2 mb-3">
                                <label x-text="'Harga Kelas ' + field.name"></label>
                                <div class="flex items-center space-x-2">
                                    <input type="hidden" :value="field.price" :name="'price[' + index + ']'">
                                    <input type="hidden" :value="field.id" :name="'id[' + index + ']'">
                                    <input type="number" x-model="field.value" :name="'harga[' + index + ']'"
                                        :placeholder="kelas"
                                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" />
                                    <button type="button" x-on:click="removeField(index)"
                                        class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-700">
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
                        </template>
                        @error('price')
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
        const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
            <path fill-rule="evenodd" d="M1.32 11.45C2.81 6.98 7.03 3.75 12 3.75c4.97 0 9.19 3.22 10.68 7.69.12.36.12.75 0 1.11C21.19 17.02 16.97 20.25 12 20.25c-4.97 0-9.19-3.22-10.68-7.69a1.76 1.76 0 0 1 0-1.11ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
        </svg>`;

        const eyeOffIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
            <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
            <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
            <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
        </svg>
        `;

        function show(e) {
            const input = e.parentElement.querySelector('input[type="password"], input[type="text"]');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                e.innerHTML = input.type === 'password' ? eyeIcon : eyeOffIcon;
            }
        }
    </script>
@endpush
