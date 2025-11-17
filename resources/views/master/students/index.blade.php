@extends('base.layout')
@section('title', 'Dashboard Master Murid')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

        <div class="mb-4 flex justify-between items-center gap-2">
            <input type="text" x-model="search" placeholder="Cari Nama"
                class="w-full md:w-1/2 border border-gray-300  ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

            <button @click="open = true"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Import
            </button>

            <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                style="display: none;" x-data=reg(kelasData)>
                <!-- Modal box -->
                <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                    <h2 class="text-xl font-bold mb-4">Import Data</h2>
                    <div class="p-4">
                        <form method="POST" action="{{ route('dashboard.master.student.store') }}"
                            enctype="multipart/form-data" x-data="{ isSubmitting: false, fileName: '' }"
                            @submit.prevent="isSubmitting = true; $el.submit()">

                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Jenjang</label>
                                    <select name="grade" required
                                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                        <option value="">Pilih Jenjang</option>
                                        @foreach ($grade as $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('grade')
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
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Pembayaran</label>
                                    <select name="kontrak" required
                                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                        <option value="">Pilih Pembayaran</option>
                                        @foreach ($kontrak as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }} ({{ $row->month }}
                                                Bulan)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kontrak')
                                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                                    <select x-model="selectedUnit" name="unit"
                                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                                        required>
                                        <template x-for="option in filteredUnits" :key="option.value">
                                            <option :value="option.value" x-text="option.label"></option>
                                        </template>
                                    </select>
                                    @error('unit')
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
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-medium text-gray-700">Pilih File:</label>
                                <input type="file" accept=".xlsx," name="file"
                                    @change="fileName = $event.target.files[0]?.name"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                       file:rounded file:border-0 file:text-sm file:font-semibold
                       file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
                                    required>

                                <template x-if="fileName">
                                    <p class="mt-2 text-gray-600 text-sm">File dipilih: <strong x-text="fileName"></strong>
                                    </p>
                                </template>

                            </div>
                            <button type="submit" :disabled="isSubmitting"
                                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline disabled:opacity-50">
                                <template x-if="!isSubmitting">
                                    <span>Simpan</span>
                                </template>
                                <template x-if="isSubmitting">
                                    <span>Mohon Tunggu...</span>
                                </template>
                            </button>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead>
                    <tr class="bg-orange-500 text-left text-white">
                        <th class="px-4 py-2">No</th>
                        <th @click="sortBy('name')" class="cursor-pointer px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Umur</th>
                        <th class="px-4 py-2">Gender</th>
                        <th class="px-4 py-2">Alamat</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in paginatedData()" :key="row.id">
                        <tr class="border-t border-gray-300">
                            <td class="px-4 py-2" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                            <td class="px-4 py-2" x-text="row.name"></td>
                            <td class="px-4 py-2" x-text="row.birth ? row.age : null"></td>
                            <td class="px-4 py-2" x-text="row.genders"></td>
                            <td class="px-4 py-2" x-text="row.alamat_sekolah"></td>
                            <td class="px-4 py-2 flex items-center gap-1">
                                <a :href="'/dashboard/master/student/' + row.id + '/edit'"
                                    class="text-orange-600 hover:text-orange-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-pencil-icon lucide-pencil">
                                        <path
                                            d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                        <path d="m15 5 4 4" />
                                    </svg>
                                </a>

                                <a :href="'/dashboard/master/user/' + md5Component(row.id) + '/detail'"
                                    class="text-orange-600 hover:text-orange-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-user-round-cog-icon lucide-user-round-cog">
                                        <path d="m14.305 19.53.923-.382" />
                                        <path d="m15.228 16.852-.923-.383" />
                                        <path d="m16.852 15.228-.383-.923" />
                                        <path d="m16.852 20.772-.383.924" />
                                        <path d="m19.148 15.228.383-.923" />
                                        <path d="m19.53 21.696-.382-.924" />
                                        <path d="M2 21a8 8 0 0 1 10.434-7.62" />
                                        <path d="m20.772 16.852.924-.383" />
                                        <path d="m20.772 19.148.924.383" />
                                        <circle cx="10" cy="8" r="5" />
                                        <circle cx="18" cy="18" r="3" />
                                    </svg>
                                </a>

                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData().length === 0">
                        <td colspan="3" class="text-center px-4 py-2 text-gray-500">No results found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-4">
            <button @click="prevPage()" :disabled="currentPage === 1"
                class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Prev</button>

            <span>Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages()"></span></span>

            <button @click="nextPage()" :disabled="currentPage === totalPages()"
                class="px-3 py-1 text-white rounded bg-orange-500 hover:bg-orange-600 disabled:opacity-50">Next</button>
        </div>
    </div>
@endsection
@push('script')
    <script>
        window.kelasData = @json($kelas);
    </script>
@endpush
