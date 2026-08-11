@extends('base.layout')
@section('title', 'Dashboard Master Murid')
@section('content')

@error('import')
<div class="text-red-500 my-4 px-4">{{ $message }}</div>
@enderror

<div class="flex flex-col bg-white rounded-lg shadow-md p-6"
    x-data="studentTable()"
    x-init="fetchData()">

    {{-- Top Bar: Search + Filter + Buttons --}}
    <div class="mb-4 flex flex-wrap gap-2 items-end justify-between">
        {{-- Search --}}
        <input type="text" x-model="search" @input.debounce.400ms="resetAndFetch()"
            placeholder="Cari Nama / Panggilan"
            class="w-full md:w-64 border border-gray-300 rounded-xl px-3 py-2 focus:outline-[#FF9966] text-sm" />

        {{-- Filters --}}
        <div class="flex flex-wrap items-end gap-2">
            <select x-model="filterGrade" @change="resetAndFetch()"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-[#FF9966]">
                <option value="">Semua Jenjang</option>
                @foreach ($grade as $val)
                <option value="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
            </select>

            <select x-model="filterUnit" @change="resetAndFetch()"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-[#FF9966]">
                <option value="">Semua Unit</option>
                @foreach ($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>

            <select x-model="filterProgram" @change="resetAndFetch()"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-[#FF9966]">
                <option value="">Semua Program</option>
                @foreach ($programs as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
                @endforeach
            </select>

            <select x-model="filterKelas" @change="resetAndFetch()"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-[#FF9966]">
                <option value="">Semua Kelas</option>
                @foreach ($kelas as $kls)
                <option value="{{ $kls->id }}">{{ $kls->name }}</option>
                @endforeach
            </select>

            <select x-model="filterGender" @change="resetAndFetch()"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-[#FF9966]">
                <option value="">Semua Gender</option>
                <option value="1">Laki-laki</option>
                <option value="2">Perempuan</option>
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2">
            <button @click="$dispatch('open-export-modal')"
                class="cursor-pointer bg-green-600 text-xs hover:bg-green-700 text-white font-semibold py-2 px-3 rounded-2xl">
                Export
            </button>
            <button @click="open = true"
                class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl">
                Import
            </button>
        </div>

        {{-- MODAL IMPORT --}}
        <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            style="display: none;" x-data="reg(kelasData)">
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
                                    class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
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
                                    class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
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
                                    class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                    <option value="">Pilih Pembayaran</option>
                                    @foreach ($kontrak as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }} ({{ $row->month }} Bulan)</option>
                                    @endforeach
                                </select>
                                @error('kontrak')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                                <select x-model="selectedUnit" name="unit"
                                    class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
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
                                    class="block border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
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
                                <p class="mt-2 text-gray-600 text-sm">File dipilih: <strong x-text="fileName"></strong></p>
                            </template>
                        </div>
                        <button type="submit" :disabled="isSubmitting"
                            class="cursor-pointer bg-orange-500 text-xs hover:bg-orange-700 text-white font-semibold py-2 px-3 rounded-2xl disabled:opacity-50">
                            <template x-if="!isSubmitting"><span>Simpan</span></template>
                            <template x-if="isSubmitting"><span>Mohon Tunggu...</span></template>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Info: Total + per_page --}}
    <div class="flex items-center justify-between mb-3 text-sm text-gray-600">
        <span>
            Total: <span class="font-semibold" x-text="total"></span> murid
            <template x-if="loading">
                <span class="ml-2 text-orange-500">⟳ Memuat...</span>
            </template>
        </span>
        <div class="flex items-center gap-2">
            <label>Tampilkan:</label>
            <select x-model="perPage" @change="resetAndFetch()"
                class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-[#FF9966]">
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead>
                <tr class="bg-orange-500 text-left text-white">
                    <th class="px-4 py-2 w-10">No</th>
                    <th class="px-4 py-2 cursor-pointer select-none" @click="sortTable('name')">
                        Nama
                        <span x-show="sortCol === 'name'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span>
                    </th>
                    <th class="px-4 py-2">Panggilan</th>
                    <th class="px-4 py-2 cursor-pointer select-none" @click="sortTable('birth')">
                        Umur
                        <span x-show="sortCol === 'birth'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span>
                    </th>
                    <th class="px-4 py-2">Gender</th>
                    <th class="px-4 py-2">Unit</th>
                    <th class="px-4 py-2">Program</th>
                    <th class="px-4 py-2">Kelas</th>
                    <th class="px-4 py-2">Jenjang</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Loading skeleton --}}
                <template x-if="loading && rows.length === 0">
                    <template x-for="i in 5" :key="i">
                        <tr class="border-t border-gray-200 animate-pulse">
                            <td colspan="10" class="px-4 py-3">
                                <div class="h-4 bg-gray-200 rounded w-full"></div>
                            </td>
                        </tr>
                    </template>
                </template>

                <template x-for="(row, index) in rows" :key="row.id">
                    <tr class="border-t border-gray-300 hover:bg-orange-50 transition-colors">
                        <td class="px-4 py-2 text-gray-500" x-text="((currentPage - 1) * perPage) + index + 1"></td>
                        <td class="px-4 py-2 font-medium text-gray-800" x-text="row.name"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.nama_panggilan"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.age ?? '-'"></td>
                        <td class="px-4 py-2">
                            <span x-show="row.gender == 1"
                                class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-semibold">L</span>
                            <span x-show="row.gender == 2"
                                class="inline-block px-2 py-0.5 text-xs rounded-full bg-pink-100 text-pink-700 font-semibold">P</span>
                            <span x-show="!row.gender" class="text-gray-400">-</span>
                        </td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.unit_name ?? '-'"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.program_name ?? '-'"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.kelas_name ?? '-'"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="row.grade_name ?? '-'"></td>
                        <td class="px-4 py-2 flex items-center gap-1">
                            {{-- Edit --}}
                            <a :href="'/dashboard/master/student/' + row.id + '/edit'"
                                class="text-orange-600 hover:text-orange-700" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                    <path d="m15 5 4 4" />
                                </svg>
                            </a>

                            {{-- Detail User --}}
                            <a :href="'/dashboard/master/user/' + md5Component(row.id) + '/detail'"
                                class="text-orange-600 hover:text-orange-700" title="Detail User">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
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

                            {{-- Delete --}}
                            <form :action="'/dashboard/master/student/' + row.id" method="POST"
                                @submit.prevent="deleteRow($event, row.id, () => fetchData())">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 disabled:opacity-50"
                                    :disabled="deletingId === row.id" title="Hapus">
                                    <template x-if="deletingId !== row.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </template>
                                    <template x-if="deletingId === row.id">
                                        <svg class="animate-spin h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>

                {{-- Empty state --}}
                <tr x-show="!loading && rows.length === 0">
                    <td colspan="10" class="text-center px-4 py-8 text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-300">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                            <span>Tidak ada data ditemukan.</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex flex-wrap justify-between items-center mt-4 gap-2">
        <span class="text-sm text-gray-600">
            Menampilkan <span x-text="rows.length > 0 ? ((currentPage - 1) * perPage + 1) : 0"></span>
            &ndash; <span x-text="(currentPage - 1) * perPage + rows.length"></span>
            dari <span x-text="total"></span> data
        </span>
        <div class="flex items-center gap-1">
            <button @click="goToPage(1)" :disabled="currentPage === 1 || loading"
                class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-40">&laquo;</button>
            <button @click="prevPage()" :disabled="currentPage === 1 || loading"
                class="px-3 py-1 text-sm rounded bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-40">Prev</button>

            {{-- Page numbers --}}
            <template x-for="p in pageNumbers()" :key="p">
                <button @click="p !== '...' && goToPage(p)" :disabled="loading"
                    :class="p === currentPage
                        ? 'bg-orange-600 text-white font-bold'
                        : p === '...'
                            ? 'cursor-default text-gray-400'
                            : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                    class="px-3 py-1 text-sm rounded" x-text="p">
                </button>
            </template>

            <button @click="nextPage()" :disabled="currentPage === lastPage || loading"
                class="px-3 py-1 text-sm rounded bg-orange-500 text-white hover:bg-orange-600 disabled:opacity-40">Next</button>
            <button @click="goToPage(lastPage)" :disabled="currentPage === lastPage || loading"
                class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-40">&raquo;</button>
        </div>
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

    function studentTable() {
        return {
            rows: [],
            total: 0,
            currentPage: 1,
            lastPage: 1,
            perPage: 15,
            search: '',
            filterGrade: '',
            filterUnit: '',
            filterProgram: '',
            filterKelas: '',
            filterGender: '',
            sortCol: 'name',
            sortDir: 'asc',
            loading: false,
            deletingId: null,
            open: false,

            datatableUrl: '{{ route("dashboard.master.student.datatable") }}',

            fetchData() {
                this.loading = true;
                const params = new URLSearchParams({
                    search:    this.search,
                    page:      this.currentPage,
                    per_page:  this.perPage,
                    grade:     this.filterGrade,
                    unit:      this.filterUnit,
                    program:   this.filterProgram,
                    kelas:     this.filterKelas,
                    gender:    this.filterGender,
                    sort:      this.sortCol,
                    direction: this.sortDir,
                });

                fetch(`${this.datatableUrl}?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.rows        = data.data;
                    this.total       = data.total;
                    this.currentPage = data.current_page;
                    this.lastPage    = data.last_page;
                    this.loading     = false;
                })
                .catch(() => { this.loading = false; });
            },

            resetAndFetch() {
                this.currentPage = 1;
                this.fetchData();
            },

            sortTable(col) {
                if (this.sortCol === col) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortCol = col;
                    this.sortDir = 'asc';
                }
                this.fetchData();
            },

            nextPage() {
                if (this.currentPage < this.lastPage) {
                    this.currentPage++;
                    this.fetchData();
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.fetchData();
                }
            },

            goToPage(p) {
                if (p >= 1 && p <= this.lastPage) {
                    this.currentPage = p;
                    this.fetchData();
                }
            },

            pageNumbers() {
                const pages = [];
                const total = this.lastPage;
                const cur   = this.currentPage;
                if (total <= 7) {
                    for (let i = 1; i <= total; i++) pages.push(i);
                } else {
                    pages.push(1);
                    if (cur > 3) pages.push('...');
                    for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
                    if (cur < total - 2) pages.push('...');
                    pages.push(total);
                }
                return pages;
            },

            md5Component(id) {
                // Use the same md5 helper available globally
                return typeof md5 === 'function' ? md5(String(id)) : id;
            },

            deleteRow(e, id, onSuccess) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Data ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mohon Tunggu...',
                            text: 'Sedang menghapus data...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); },
                        });

                        this.deletingId = id;
                        const form = e.target;
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            this.deletingId = null;
                            if (res.ok) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message || 'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                }).then(() => {
                                    if (onSuccess) onSuccess();
                                    else this.fetchData();
                                });
                            } else {
                                Swal.fire('Error', data.message || 'Gagal menghapus data', 'error');
                            }
                        })
                        .catch(() => {
                            this.deletingId = null;
                            Swal.fire('Error', 'Gagal menghubungi server', 'error');
                        });
                    }
                });
            },
        };
    }
</script>
@endpush