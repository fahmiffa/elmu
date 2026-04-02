@extends('base.layout')
@section('title', 'Salary - Teacher Payroll')
@section('breadcrumb')
Salary Overview
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-md p-6" x-data="salaryComponent">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Rekap Salary</h2>
        
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
                        <th class="px-6 py-4 text-left">Miska</th>
                        <th class="px-6 py-4 text-left">Unit</th>
                        <th class="px-6 py-4 text-center text-blue-600">Jumlah</th>
                        <th class="px-6 py-4 text-right text-green-600">Total</th>
                        <th class="px-6 py-4 text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $teach)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($teach->salaries->isEmpty())
                                <input type="checkbox" value="{{ $teach->id }}" 
                                    x-model="selectedTeachers"
                                    class="teacher-checkbox rounded text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer">
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $teach->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $teach->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-bold text-sm">
                                {{ $teach->present_count }} Siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($teach->salaries->isNotEmpty())
                                <span class="font-bold text-green-700">Rp {{ number_format($teach->salaries->first()->total, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400 italic text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($teach->salaries->isNotEmpty())
                                <span class="text-xs text-gray-600 font-medium">{{ \Carbon\Carbon::parse($teach->salaries->first()->created_at)->translatedFormat('d M Y, H:i') }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded text-[10px] font-bold uppercase">Belum Tergenerate</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400 italic">Data guru tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-8 p-4 bg-orange-50 rounded-lg border border-orange-100">
        <p class="text-xs text-orange-700 leading-relaxed">
            <strong>Catatan:</strong> Jumlah mengajar dihitung berdasarkan total kehadiran siswa
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
                    <!-- Form for submissions -->
                    <form id="processForm" action="{{ route('dashboard.salary.generate') }}" method="POST" x-ref="processForm">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <template x-for="(guru, index) in selectedTeachersData" :key="guru.id">
                            <div>
                                <input type="hidden" :name="'teachers['+index+'][id]'" :value="guru.id">
                                <input type="hidden" :name="'teachers['+index+'][sessions]'" :value="guru.sessions">
                                <input type="hidden" :name="'teachers['+index+'][percentage]'" :value="guru.percentage">
                                <input type="hidden" :name="'teachers['+index+'][total]'" :value="calculateTeacherTotal(guru)">
                                <input type="hidden" :name="'teachers['+index+'][jumlah_pertemuan]'" :value="Object.values(guru.groupedPresents).reduce((acc, curr) => acc + curr.totalAttendance, 0)">
                            </div>
                        </template>
                    </form>

                    <div class="p-6">
                        <div class="flex items-start justify-between mb-8">
                            <div class="p-2 bg-orange-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                            </div>
                            <div class="flex-1 ml-4">
                                <h3 class="text-xl font-black text-gray-900 leading-none mb-1">
                                    Konfirmasi Generate Salary
                                </h3>
                                <p class="text-xs text-gray-500 font-medium">Tinjau kehadiran dan tentukan parameter gaji per guru.</p>
                            </div>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        
                        <!-- Search Filter -->
                        <div class="mb-6">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <input type="text" x-model="searchQuery" 
                                    placeholder="Cari nama guru..." 
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-100 rounded-xl leading-5 bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm transition-all">
                            </div>
                        </div>

                        <!-- List per Guru -->
                        <div class="space-y-12 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar no-scrollbar">
                            <template x-for="guru in filteredTeachers" :key="guru.id">
                                <div class="relative">
                                    <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-sm py-2 mb-4 border-b flex items-center justify-between">
                                        <div>
                                            <h4 class="text-lg font-black text-gray-900" x-text="guru.name"></h4>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider" x-text="guru.unit"></p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="flex flex-col">
                                                <label class="text-[9px] font-black text-blue-700 uppercase">Sesi</label>
                                                <input type="number" x-model="guru.sessions" class="w-16 px-2 py-1 border rounded bg-blue-50 text-xs font-bold text-blue-700 focus:ring-0">
                                            </div>
                                            <div class="flex flex-col">
                                                <label class="text-[9px] font-black text-purple-700 uppercase">Persentase (%)</label>
                                                <input type="number" x-model="guru.percentage" class="w-16 px-2 py-1 border rounded bg-purple-50 text-xs font-bold text-purple-700 focus:ring-0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <template x-for="(group, programName) in guru.groupedPresents" :key="programName">
                                            <div class="bg-gray-50/50 border border-gray-100 rounded-xl overflow-hidden">
                                                <div class="px-4 py-2 flex justify-between items-center border-b border-gray-100">
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-[9px] font-black uppercase" x-text="programName"></span>
                                                        <span class="text-[10px] font-bold text-gray-400" x-text="group.totalAttendance + ' Siswa'"></span>
                                                    </div>
                                                    <div class="text-[10px] font-black text-gray-600">
                                                        @ <span x-text="formatCurrency(group.harga)"></span>
                                                    </div>
                                                </div>
                                                <div class="p-2">
                                                    <template x-for="item in group.students" :key="item.id">
                                                        <div class="flex justify-between items-center py-1 px-2 hover:bg-white rounded transition-colors group">
                                                            <span class="text-xs text-gray-700 font-medium">
                                                                <span x-text="item.nama"></span>
                                                                <span x-show="item.count > 1" class="text-blue-600 font-bold ml-1" x-text="'(' + item.count + 'x)'"></span>
                                                            </span>
                                                            <span class="text-[10px] text-gray-400 italic opacity-0 group-hover:opacity-100 transition-opacity" x-text="item.nama_panggilan"></span>
                                                        </div>
                                                    </template>

                                                    <!-- Rincian Hitung -->
                                                    <div class="mt-2 p-3 bg-white border border-dashed border-gray-200 rounded-lg">
                                                        <div class="flex flex-col gap-1">
                                                            <div class="flex justify-between items-center text-[9px] text-gray-500 uppercase font-black">
                                                                <span>Rumus</span>
                                                                <span>Nominal / Siswa</span>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="text-[10px] text-gray-400 font-medium">
                                                                    (<span x-text="formatCurrency(group.harga)"></span> &times; <span x-text="guru.percentage + '%'"></span>) &divide; <span x-text="guru.sessions"></span>
                                                                </div>
                                                                <div class="text-xs font-black text-orange-600" x-text="formatCurrency(calculateNominal(group.harga, guru.percentage, guru.sessions))"></div>
                                                            </div>
                                                            <div class="mt-2 pt-2 border-t border-gray-50 flex justify-between items-center">
                                                                <span class="text-[10px] font-black text-gray-700 uppercase">Sub-Total Program</span>
                                                                <span class="text-sm font-black text-green-700 underline decoration-2 decoration-green-200 underline-offset-4" x-text="formatCurrency(calculateSubtotal(group, guru.percentage, guru.sessions))"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Total Gaji Guru -->
                                    <div class="mt-4 pt-4 border-t-2 border-dashed border-gray-100 flex justify-between items-center bg-green-50/50 p-4 rounded-xl">
                                        <div>
                                            <span class="text-[10px] font-black text-green-700 uppercase tracking-widest block">Estimasi Penerimaan Gaji</span>
                                            <span class="text-[9px] text-green-600 font-bold uppercase" x-text="guru.name"></span>
                                        </div>
                                        <div class="text-xl font-black text-green-700" x-text="formatCurrency(calculateTeacherTotal(guru))"></div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredTeachers.length === 0">
                                <div class="px-6 py-20 text-center bg-gray-50/50 rounded-3xl border-2 border-dashed border-gray-100">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" class="text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <p class="text-xs text-gray-400 font-medium" x-text="searchQuery ? 'Guru dengan nama \'' + searchQuery + '\' tidak ditemukan.' : 'Tidak ada data kehadiran siswa untuk guru yang dipilih.'"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-100 rounded-b-3xl">
                        <button type="submit" form="processForm"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-8 py-3 bg-green-600 text-base font-black text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-all hover:scale-105 active:scale-95 shadow-green-200">
                            Konfirmasi & Proses
                        </button>
                        <button type="button" @click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-8 py-3 bg-white text-base font-bold text-gray-600 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
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
        searchQuery: '',
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
            this.searchQuery = '';
            this.selectedTeachersData = [];
            this.selectedTeachers.forEach(id => {
                if (this.teachersDetails[id] && this.teachersDetails[id].presents.length > 0) {
                    const teacher = this.teachersDetails[id];
                    const groups = {};
                    
                    teacher.presents.forEach(p => {
                        if (!groups[p.program]) {
                            groups[p.program] = {
                                name: p.program,
                                harga: p.harga,
                                harga_formatted: p.harga_formatted,
                                students: [],
                                totalAttendance: 0
                            };
                        }
                        
                        groups[p.program].totalAttendance++;

                        let existingStudent = groups[p.program].students.find(s => s.nama === p.nama);
                        if (existingStudent) {
                            existingStudent.count++;
                        } else {
                            groups[p.program].students.push({
                                ...p,
                                count: 1
                            });
                        }
                    });

                    this.selectedTeachersData.push({
                        id: teacher.id,
                        name: teacher.name,
                        unit: teacher.unit,
                        sessions: 8,
                        percentage: 25,
                        groupedPresents: groups
                    });
                }
            });

            this.showModal = true;
        },
        get filteredTeachers() {
            if (this.searchQuery.trim() === '') return this.selectedTeachersData;
            return this.selectedTeachersData.filter(guru => 
                guru.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        calculateNominal(harga, percentage, sessions) {
            if (!sessions || sessions == 0) return 0;
            return (harga * (percentage / 100)) / sessions;
        },
        calculateSubtotal(group, percentage, sessions) {
            const nominal = this.calculateNominal(group.harga, percentage, sessions);
            return nominal * group.totalAttendance;
        },
        calculateTeacherTotal(guru) {
            let total = 0;
            Object.values(guru.groupedPresents).forEach(group => {
                total += this.calculateSubtotal(group, guru.percentage, guru.sessions);
            });
            return total;
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }
    }))
})
</script>
@endpush
@endsection
