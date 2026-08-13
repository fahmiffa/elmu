@extends('base.layout')
@section('title', 'Dashboard Master Murid')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTable({{ json_encode($items) }})">

    @error('import')
    <div class="text-red-500 my-10">{{ $message }}</div>
    @enderror


    <div class="mb-4 flex justify-between items-center gap-2">
        <input type="text" x-model="search" placeholder="Cari Nama"
            class="w-full md:w-1/2 border border-gray-300 ring-0 rounded-xl px-3 py-2 focus:outline-[#FF9966]" />

        <div class="flex items-center gap-2">
            {{-- Tombol Export --}}
            <button @click="$dispatch('open-export-modal')"
                class="cursor-pointer bg-green-600 text-xs hover:bg-green-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Export
            </button>

            <button @click="open = true"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                Import
            </button>
        </div>

        {{-- MODAL IMPORT --}}
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
                    <th class="px-4 py-2 text-nowrap">No. Induk</th>
                    <th class="px-4 py-2">Panggilan</th>
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
                        <td class="px-4 py-2 text-nowrap text-xs font-mono" x-text="row.nomor_induk ?? '-'"></td>
                        <td class="px-4 py-2" x-text="row.nama_panggilan"></td>
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

                            <form :action="'/dashboard/master/student/' + row.id" method="POST"
                                @submit.prevent="deleteRow($event, row.id)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 disabled:opacity-50" :disabled="deletingId === row.id">
                                    <template x-if="deletingId !== row.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-trash-2-icon lucide-trash-2">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </template>
                                    <template x-if="deletingId === row.id">
                                        <svg class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                </button>
                            </form>

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

{{-- MODAL EXPORT --}}
<div x-data="{
    exportModal: false,
    isExporting: false,
    isDone: false,
    isError: false,
    init() {
        this.$watch('exportModal', value => {
            if (value) document.body.classList.add('overflow-hidden');
            else document.body.classList.remove('overflow-hidden');
        });
    },
    errorMsg: '',
    downloadUrl: '',
    statusUrl: '',
    progress: 0,
    pollInterval: null,

    startExport(formEl) {
        this.isExporting = true;
        this.isDone = false;
        this.isError = false;
        this.progress = 5;

        const formData = new FormData(formEl);
        fetch(formEl.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            this.statusUrl = data.status_url;
            this.progress = 15;
            this.pollStatus();
        })
        .catch(() => {
            this.isExporting = false;
            this.isError = true;
            this.errorMsg = 'Gagal menghubungi server. Coba lagi.';
        });
    },

    pollStatus() {
        let tick = 0;
        this.pollInterval = setInterval(() => {
            tick++;
            if (this.progress < 85) this.progress += Math.random() * 4;

            fetch(this.statusUrl)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'done') {
                        clearInterval(this.pollInterval);
                        this.progress = 100;
                        this.downloadUrl = data.download_url;
                        setTimeout(() => { this.isDone = true; this.isExporting = false; }, 400);
                    } else if (data.status === 'error') {
                        clearInterval(this.pollInterval);
                        this.isExporting = false;
                        this.isError = true;
                        this.errorMsg = data.message ?? 'Terjadi kesalahan saat export.';
                    }
                });

            if (tick > 150) {
                clearInterval(this.pollInterval);
                this.isExporting = false;
                this.isError = true;
                this.errorMsg = 'Proses export terlalu lama. Coba lagi.';
            }
        }, 2000);
    },

    resetModal() {
        if (this.pollInterval) clearInterval(this.pollInterval);
        this.isExporting = false;
        this.isDone = false;
        this.isError = false;
        this.errorMsg = '';
        this.downloadUrl = '';
        this.progress = 0;
        this.exportModal = false;
    }
}"
    @open-export-modal.window="exportModal = true">
    <div x-show="exportModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        style="display: none;">
        <div @click.away="resetModal()" class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">

            <h2 class="text-xl font-bold mb-1">Export Data Siswa</h2>
            <p class="text-sm text-gray-500 mb-4">Pilih filter, lalu klik <strong>Export Sekarang</strong>. Proses berjalan di background.</p>

            {{-- FORM FILTER --}}
            <form method="POST" action="{{ route('dashboard.master.student.export') }}"
                x-ref="exportForm"
                x-show="!isExporting && !isDone && !isError"
                @submit.prevent="startExport($refs.exportForm)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">Jenjang</label>
                        <select name="grade" class="block border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966] text-sm">
                            <option value="">Semua Jenjang</option>
                            @foreach ($grade as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">Unit</label>
                        <select name="unit" class="block border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966] text-sm">
                            <option value="">Semua Unit</option>
                            @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">Kelas</label>
                        <select name="kelas" class="block border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966] text-sm">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">Program</label>
                        <select name="program" class="block border border-gray-300 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966] text-sm">
                            <option value="">Semua Program</option>
                            @foreach ($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 mt-1">
                    <button type="submit"
                        class="cursor-pointer bg-green-600 text-sm hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-xl">
                        Export Sekarang
                    </button>
                    <button type="button" @click="resetModal()"
                        class="cursor-pointer bg-gray-200 text-sm hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-xl">
                        Batal
                    </button>
                </div>
            </form>

            {{-- PROGRESS STATE --}}
            <div x-show="isExporting" class="py-4">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Sedang mengekspor data siswa...</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full bg-gradient-to-r from-green-400 to-green-600 transition-all duration-700 ease-out"
                        :style="'width: ' + progress + '%'"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-right" x-text="Math.round(progress) + '%'"></p>
                <p class="text-xs text-gray-400 mt-1">Jangan tutup halaman ini. Proses berjalan di background.</p>
            </div>

            {{-- DONE STATE --}}
            <div x-show="isDone" class="py-4 text-center">
                <div class="flex justify-center mb-3">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <p class="font-semibold text-gray-800 mb-1">Export Selesai!</p>
                <p class="text-sm text-gray-500 mb-4">File Excel sudah siap diunduh.</p>
                <div class="flex gap-2 justify-center">
                    <a :href="downloadUrl"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-5 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Excel
                    </a>
                    <button @click="resetModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold py-2 px-4 rounded-xl">
                        Tutup
                    </button>
                </div>
            </div>

            {{-- ERROR STATE --}}
            <div x-show="isError" class="py-4 text-center">
                <div class="flex justify-center mb-3">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <p class="font-semibold text-gray-800 mb-1">Export Gagal</p>
                <p class="text-sm text-red-500 mb-4" x-text="errorMsg"></p>
                <button @click="isError = false; isExporting = false; isDone = false;"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold py-2 px-4 rounded-xl">
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    window.kelasData = @json($kelas);
</script>
@endpush