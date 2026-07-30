@extends('base.layout')
@section('title', 'Edit Permit')

@push('styles')
{{-- Tom Select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.single .ts-control {
        @apply px-3 py-2 border rounded-lg bg-white;
        cursor: text;
    }
    .ts-wrapper.single.input-active .ts-control { border-color: #f97316; box-shadow: 0 0 0 1px #f97316; }
    .ts-dropdown .option.selected { background: #fed7aa; color: #7c2d12; }
    .ts-dropdown .option:hover, .ts-dropdown .option.active { background: #fff7ed; color: #ea580c; }
    .ts-dropdown { border-radius: .5rem; border-color: #e5e7eb; box-shadow: 0 4px 16px rgba(0,0,0,.1); }
</style>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Edit Permit</h2>
        <a href="{{ route('dashboard.presensi.index') }}" class="text-gray-500 hover:text-gray-700">Kembali</a>
    </div>

    <form action="{{ route('dashboard.presensi.update', $presensi->id) }}" method="POST" id="form-presensi">
        @csrf
        @method('PUT')

        {{-- ── Dari Tanggal ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Dari Tanggal</label>
            <input type="date" id="tanggal" name="tanggal"
                   value="{{ old('tanggal', $presensi->tanggal) }}" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
            <p id="tanggal_label" class="text-sm font-semibold text-orange-600 mt-1"></p>
            <p id="tanggal_error" class="text-sm text-red-500 mt-1 hidden"></p>
            @error('tanggal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- ── Nama Siswa (Tom Select Search) ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Nama Siswa</label>
            <select name="student_id" id="student_id" required>
                <option value="">Pilih Siswa</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}"
                        {{ old('student_id', $presensi->student_id) == $student->id ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>
            @error('student_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            {{-- Info card siswa --}}
            <div id="student_info" class="hidden mt-3 bg-orange-50 border border-orange-200 rounded-lg px-4 py-3">
                <p class="font-semibold text-gray-800" id="info_name"></p>
                <div class="flex flex-wrap gap-4 mt-1 text-sm text-gray-600">
                    <span>🏫 <span id="info_unit"></span></span>
                    <span>📚 <span id="info_program"></span></span>
                </div>
            </div>
        </div>

        {{-- ── Dari Sesi Jadwal ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Dari Sesi Jadwal</label>
            <select name="schedule_student_id" id="schedule_student_id" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                <option value="">Pilih Sesi Jadwal</option>
                @foreach($schedules as $sch)
                    @foreach($sch->sch as $session)
                        <option value="{{ $sch->id }}"
                                data-hari="{{ strtolower($session->parse ?? '') }}"
                                {{ old('schedule_student_id', $presensi->schedule_student_id) == $sch->id ? 'selected' : '' }}>
                            {{ $session->parse ?? 'Jadwal' }} - {{ $session->name ?? 'Sesi' }} ({{ $session->start_time }} s/d {{ $session->end_time }})
                        </option>
                    @endforeach
                @endforeach
            </select>
            @error('schedule_student_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- ── Ke Sesi Jadwal ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Ke Sesi Jadwal</label>
            <select name="unit_schedules_id" id="unit_schedules_id" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
                <option value="">Pilih Ke Sesi Jadwal</option>
                @foreach($unitSchedules as $sch)
                    <option value="{{ $sch->id }}"
                            data-hari="{{ strtolower($sch->parse ?? '') }}"
                            {{ old('unit_schedules_id', $presensi->unit_schedules_id) == $sch->id ? 'selected' : '' }}>
                        {{ $sch->parse ?? 'Jadwal' }} - {{ $sch->name ?? 'Sesi' }} ({{ $sch->start_time }} s/d {{ $sch->end_time }})
                    </option>
                @endforeach
            </select>
            @error('unit_schedules_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- ── Tanggal Pengganti ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">
                Tanggal Pengganti
                <span class="text-xs font-normal text-gray-500">(ganti ke tanggal ini)</span>
            </label>
            <input type="date" id="new_date" name="new_date"
                   value="{{ old('new_date', $presensi->new_date) }}" required
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500">
            <p id="new_date_label" class="text-sm font-semibold text-blue-600 mt-1"></p>
            <p id="new_date_error" class="text-sm text-red-500 mt-1 hidden"></p>
            @error('new_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- ── Keterangan ── --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Keterangan</label>
            <textarea name="why" rows="4" required
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500">{{ old('why', $presensi->why) }}</textarea>
            @error('why') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" id="btn-submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-6 rounded">
                Update
            </button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    const daysMapEN   = ['minggu','senin','selasa','rabu','kamis','jumat','sabtu'];
    const daysMapID   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const monthsMapID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Helpers ───────────────────────────────────────────────────────────────
    function parseDateLocal(val) {
        const [y, m, d] = val.split('-').map(Number);
        return new Date(y, m - 1, d);
    }
    function formatHariTanggal(val) {
        if (!val) return '';
        const d = parseDateLocal(val);
        return 'Hari ' + daysMapID[d.getDay()] + ', tanggal ' + d.getDate() + ' ' + monthsMapID[d.getMonth()] + ' ' + d.getFullYear();
    }

    // ── Tom Select for Siswa ──────────────────────────────────────────────────
    const tsStudent = new TomSelect('#student_id', {
        placeholder: 'Cari nama siswa...',
        allowEmptyOption: true,
        maxOptions: 200,
        onChange(val) { onStudentChange(val); }
    });

    function loadStudentInfo(studentId) {
        const infoBox = document.getElementById('student_info');
        fetch('{{ route("dashboard.presensi.get-student-info") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { infoBox.classList.add('hidden'); return; }
            document.getElementById('info_name').textContent    = data.name;
            document.getElementById('info_unit').textContent    = data.unit;
            document.getElementById('info_program').textContent = data.program;
            infoBox.classList.remove('hidden');
        })
        .catch(() => infoBox.classList.add('hidden'));
    }

    function onStudentChange(studentId) {
        const infoBox  = document.getElementById('student_info');
        const schedSel = document.getElementById('schedule_student_id');

        // Reset
        infoBox.classList.add('hidden');
        schedSel.innerHTML = '<option value="">Pilih Sesi Jadwal</option>';
        document.getElementById('tanggal_error').classList.add('hidden');

        if (!studentId) return;

        loadStudentInfo(studentId);

        // ── Reload jadwal ────────────────────────────────────────────────────
        schedSel.innerHTML = '<option value="">Memuat jadwal...</option>';
        fetch('{{ route("dashboard.presensi.get-schedule") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(r => r.json())
        .then(data => {
            schedSel.innerHTML = data.options;
            checkTanggalAsal();
        })
        .catch(() => { schedSel.innerHTML = '<option value="">Gagal memuat jadwal</option>'; });
    }

    // ── Validasi: Dari Tanggal vs Dari Sesi Jadwal ────────────────────────────
    function updateTanggalLabel() {
        const val = document.getElementById('tanggal').value;
        document.getElementById('tanggal_label').textContent = val ? formatHariTanggal(val) : '';
    }

    function checkTanggalAsal() {
        const dateVal  = document.getElementById('tanggal').value;
        const schedSel = document.getElementById('schedule_student_id');
        const selOpt   = schedSel.options[schedSel.selectedIndex];
        const errEl    = document.getElementById('tanggal_error');

        if (!dateVal || !selOpt || !selOpt.value) { errEl.classList.add('hidden'); return false; }

        const hariJadwal  = selOpt.getAttribute('data-hari');
        if (!hariJadwal)  { errEl.classList.add('hidden'); return false; }

        const hariTanggal = daysMapEN[parseDateLocal(dateVal).getDay()];
        if (hariJadwal !== hariTanggal) {
            errEl.textContent = '⚠ Tanggal asal harus hari '
                + hariJadwal.charAt(0).toUpperCase() + hariJadwal.slice(1)
                + ' (sesuai sesi jadwal asal), Anda memilih hari '
                + hariTanggal.charAt(0).toUpperCase() + hariTanggal.slice(1) + '.';
            errEl.classList.remove('hidden');
            return true;
        } else {
            errEl.classList.add('hidden');
            return false;
        }
    }

    // ── Validasi: Tanggal Pengganti vs Ke Sesi Jadwal ────────────────────────
    function updateNewDateLabel() {
        const val = document.getElementById('new_date').value;
        document.getElementById('new_date_label').textContent = val ? formatHariTanggal(val) : '';
        checkNewDate();
    }

    function checkNewDate() {
        const dateVal = document.getElementById('new_date').value;
        const unitSel = document.getElementById('unit_schedules_id');
        const selOpt  = unitSel.options[unitSel.selectedIndex];
        const errEl   = document.getElementById('new_date_error');

        if (!dateVal || !selOpt || !selOpt.value) { errEl.classList.add('hidden'); return false; }

        const hariJadwal  = selOpt.getAttribute('data-hari');
        if (!hariJadwal)  { errEl.classList.add('hidden'); return false; }

        const hariTanggal = daysMapEN[parseDateLocal(dateVal).getDay()];
        if (hariJadwal !== hariTanggal) {
            errEl.textContent = '⚠ Tanggal pengganti harus hari '
                + hariJadwal.charAt(0).toUpperCase() + hariJadwal.slice(1)
                + ' (sesuai sesi tujuan), Anda memilih hari '
                + hariTanggal.charAt(0).toUpperCase() + hariTanggal.slice(1) + '.';
            errEl.classList.remove('hidden');
            return true;
        } else {
            errEl.classList.add('hidden');
            return false;
        }
    }

    // ── Blokir submit jika ada error validasi hari ────────────────────────────
    document.getElementById('form-presensi').addEventListener('submit', function (e) {
        const errAsal = checkTanggalAsal();
        const errBaru = checkNewDate();
        if (errAsal || errBaru) {
            e.preventDefault();
            if (errAsal) document.getElementById('tanggal').scrollIntoView({ behavior: 'smooth', block: 'center' });
            else         document.getElementById('new_date').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // ── Event listeners ───────────────────────────────────────────────────────
    document.getElementById('tanggal').addEventListener('change', function () {
        updateTanggalLabel();
        checkTanggalAsal();
    });
    document.getElementById('schedule_student_id').addEventListener('change', checkTanggalAsal);
    document.getElementById('new_date').addEventListener('change', updateNewDateLabel);
    document.getElementById('unit_schedules_id').addEventListener('change', checkNewDate);

    // ── Init: load pada edit (siswa sudah terpilih) ───────────────────────────
    updateTanggalLabel();
    updateNewDateLabel();

    // Tampilkan info siswa yang sudah terpilih saat halaman edit dibuka
    const initialStudentId = '{{ old("student_id", $presensi->student_id) }}';
    if (initialStudentId) {
        loadStudentInfo(initialStudentId);
    }
</script>
@endpush
