@extends('base.layout')
@section('title', 'Salary - Teacher Payroll')
@section('breadcrumb')
Salary Overview
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-md p-6" x-data="salaryComponent">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Rekap Gaji / Mengajar Guru</h2>
        
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('dashboard.salary') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="unit" class="px-3 py-2 border rounded-lg text-sm bg-gray-50 focus:ring-orange-500">
                    <option value="">Semua Unit</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ $unit == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                <select name="month" class="px-3 py-2 border rounded-lg text-sm bg-gray-50 focus:ring-orange-500">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="px-3 py-2 border rounded-lg text-sm bg-gray-50 focus:ring-orange-500">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-300 transition-colors">
                    Filter
                </button>
            </form>

            <button type="button" @click="openSalaryModal()" 
                class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition-colors shadow-md flex items-center gap-2"
                :disabled="selectedTeachers.length === 0"
                :class="selectedTeachers.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                Generate Salary (<span x-text="selectedTeachers.length"></span>)
            </button>
        </div>
    </div>

    <form id="generateForm" action="{{ route('dashboard.salary.generate') }}" method="POST">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="sessions" :value="sessions">
        <input type="hidden" name="percentage" :value="percentage">
        
        <template x-for="id in selectedTeachers" :key="id">
            <input type="hidden" name="teachers[]" :value="id">
        </template>
        
        <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase">
                    <tr>
                        <th class="px-6 py-4 text-left w-10">
                            <input type="checkbox" @click="toggleAll()" :checked="selectAll" class="rounded text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Nama Guru</th>
                        <th class="px-6 py-4 text-left">Unit / Cabang</th>
                        <th class="px-6 py-4 text-center text-blue-600">Jumlah Mengajar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $teach)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $teach->id }}" 
                                x-model="selectedTeachers"
                                class="teacher-checkbox rounded text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $teach->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $teach->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold text-sm">
                                {{ $teach->present_count }} Siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold uppercase">Active</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data guru tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-8 p-4 bg-orange-50 rounded-lg border border-orange-100">
        <p class="text-xs text-orange-700 leading-relaxed">
            <strong>Catatan:</strong> Jumlah mengajar dihitung berdasarkan total kehadiran siswa (Student Present) yang diinput oleh guru selama periode terpilih. Satu sesi mengajar dengan 5 siswa akan terhitung sebagai 5 poin mengajar.
        </p>
    </div>

    <!-- Modal Detail Siswa -->
    <div x-show="showModal" class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="showModal = false"
                class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 relative z-10">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                Konfirmasi Generate Salary
                            </h3>

                            <!-- Nama Guru Seleksi -->
                            <div class="mb-6">
                                <label class="text-xs font-bold text-gray-500 uppercase block mb-2">Guru yang dipilih:</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="guru in selectedTeachersData" :key="guru.id">
                                        <span class="px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 rounded-full text-xs font-bold flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            <span x-text="guru.name"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Input Sesi & Persentase -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                                    <label class="text-xs font-bold text-blue-700 uppercase block mb-1">Jumlah Sesi</label>
                                    <input type="number" x-model="sessions" class="w-full bg-white border border-blue-200 rounded-lg px-3 py-2 text-sm font-bold focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-[10px] text-blue-600 mt-1">* Digunakan sebagai pengali dalam perhitungan gaji.</p>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-xl border border-purple-100">
                                    <label class="text-xs font-bold text-purple-700 uppercase block mb-1">Persentase (%)</label>
                                    <input type="number" x-model="percentage" class="w-full bg-white border border-purple-200 rounded-lg px-3 py-2 text-sm font-bold focus:ring-purple-500 focus:border-purple-500">
                                    <p class="text-[10px] text-purple-600 mt-1">* Persentase gaji yang akan diterima guru.</p>
                                </div>
                            </div>
                            
                            <!-- Groups per Program -->
                            <div class="mt-4 space-y-6 max-h-[400px] overflow-y-auto pr-2">
                                <template x-for="(group, programName) in groupedPresents" :key="programName">
                                    <div class="border rounded-xl overflow-hidden shadow-sm">
                                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                            <div class="flex items-center gap-2">
                                                <span class="p-1 px-2 bg-orange-600 text-white rounded text-[10px] font-bold uppercase" x-text="programName"></span>
                                                <span class="text-sm font-bold text-gray-700" x-text="group.students.length + ' Siswa'"></span>
                                            </div>
                                            <div class="text-sm font-bold text-blue-700">
                                                @ <span x-text="'Rp ' + group.harga_formatted"></span>
                                            </div>
                                        </div>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                <template x-for="item in group.students" :key="item.id">
                                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                                        <td class="px-6 py-3 text-sm text-gray-800 font-medium" x-text="item.nama"></td>
                                                        <td class="px-6 py-3 text-sm text-gray-500" x-text="item.nama_panggilan"></td>
                                                        <td class="px-6 py-3 text-right text-sm text-gray-400 italic" x-text="item.unit"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                <template x-if="Object.keys(groupedPresents).length === 0">
                                    <div class="px-6 py-12 text-center text-gray-400 italic text-sm bg-gray-50/20 rounded-xl border border-dashed">
                                        Tidak ada data kehadiran siswa pada periode ini.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" form="generateForm"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-green-600 text-base font-bold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-all hover:scale-105 active:scale-95">
                        Lanjutkan Generate
                    </button>
                    <button type="button" @click="showModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
@php
    $teachersDetails = $items->mapWithKeys(function($t) {
        return [$t->id => [
            'id' => $t->id,
            'name' => $t->name,
            'unit' => $t->unit->name ?? '-',
            'presents' => $t->present->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->student?->name ?? '-',
                    'nama_panggilan' => $p->student?->nama_panggilan ?? '-',
                    'program' => $p->program?->name ?? '-',
                    'unit' => $p->reg?->units?->name ?? '-',
                    'harga' => $p->reg?->product?->harga ?? 0,
                    'harga_formatted' => number_format($p->reg?->product?->harga ?? 0, 0, ',', '.'),
                ];
            })
        ]];
    });
@endphp

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('salaryComponent', () => ({
        selectedTeachers: [],
        selectAll: false,
        showModal: false,
        selectedTeachersData: [],
        groupedPresents: {},
        sessions: 1,
        percentage: 100,
        teachersDetails: @json($teachersDetails),

        init() {
            this.$watch('selectedTeachers', (value) => {
                const total = document.querySelectorAll('.teacher-checkbox').length;
                this.selectAll = value.length > 0 && value.length === total;
            });
        },
        toggleAll() {
            const checkboxes = Array.from(document.querySelectorAll('.teacher-checkbox'));
            if (this.selectedTeachers.length === checkboxes.length) {
                this.selectedTeachers = [];
            } else {
                this.selectedTeachers = checkboxes.map(cb => cb.value);
            }
        },
        openSalaryModal() {
            this.selectedTeachersData = [];
            let allPresents = [];
            this.selectedTeachers.forEach(id => {
                if (this.teachersDetails[id]) {
                    this.selectedTeachersData.push(this.teachersDetails[id]);
                    allPresents = [...allPresents, ...this.teachersDetails[id].presents];
                }
            });

            // Grouping by program
            const groups = {};
            allPresents.forEach(p => {
                if (!groups[p.program]) {
                    groups[p.program] = {
                        name: p.program,
                        harga: p.harga,
                        harga_formatted: p.harga_formatted,
                        students: []
                    };
                }
                groups[p.program].students.push(p);
            });
            this.groupedPresents = groups;

            this.showModal = true;
        }
    }))
})
</script>
@endpush
@endsection
