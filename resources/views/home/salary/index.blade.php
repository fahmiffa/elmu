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

            <button type="submit" form="generateForm" 
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
                            <input type="checkbox" name="teachers[]" value="{{ $teach->id }}" 
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
</div>

@push('script')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('salaryComponent', () => ({
        selectedTeachers: [],
        selectAll: false,
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
        }
    }))
})
</script>
@endpush
@endsection
