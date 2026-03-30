@extends('base.layout')

@section('title', 'Form Raport')

@section('content')
    {{-- ============================================================
         Build student list with unit_id, kelas_id, program_name, etc.
         dari relasi model Head (reg → units / class / programs / grade)
    ============================================================ --}}
    @php
        $studentData = $student->map(function ($s) {
            $reg = $s->reg->where('done', 0)->first();
            return [
                'user'         => $s->user,
                'name'         => $s->name,
                'nama_panggilan' => $s->nama_panggilan ?? null,
                'unit_id'      => $reg?->units?->id      ?? null,
                'kelas_id'     => $reg?->class?->id      ?? null,
                'program_id'   => $reg?->programs?->id   ?? null,
                'program_name' => $reg?->programs?->name ?? '',
                'grade_name'   => $s->grade?->name       ?? '',
            ];
        });
    @endphp

    <div class="flex flex-col bg-white rounded-lg shadow-md p-6"
         x-data="raportForm(
             {{ $studentData->toJson() }},
             {{ $units->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toJson() }},
             {{ $kelas->map(fn($k) => ['id' => $k->id, 'name' => $k->name])->toJson() }},
             {{ $programs->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->toJson() }},
             {{ $teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'unit_id' => $t->unit_id])->toJson() }},
             {
                 studentId:  '{{ old('student_id',  $items->student_id  ?? '') }}',
                 program:    '{{ old('program',      $items->program     ?? '') }}',
                 levelPeriod:'{{ old('level_period', $items->level_period ?? '') }}',
                 teacher:    '{{ old('teacher',      $items->teacher      ?? '') }}'
             }
         )">

        <div class="font-semibold mb-6 text-2xl text-gray-800 border-b pb-3">{{ $action }}</div>

        <form method="POST"
            action="{{ isset($items) ? route('dashboard.raport.update', ['raport' => $items->id]) : route('dashboard.raport.store') }}"
            enctype="multipart/form-data" class="space-y-6">
            @isset($items)
                @method('PUT')
            @endisset
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ============================================================
                     FILTER SISWA (Unit, Kelas, Program)
                ============================================================ --}}
                <div class="md:col-span-2">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2 mb-3">Filter Pencarian Siswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-orange-50 border border-orange-200 rounded-xl p-4">

                        {{-- Filter Unit --}}
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Filter Unit
                            </label>
                            <select x-ref="selectUnit"
                                class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                                <option value="">-- Semua Unit --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Kelas --}}
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253" />
                                </svg>
                                Filter Kelas
                            </label>
                            <select x-ref="selectKelas"
                                class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Program --}}
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Filter Program
                            </label>
                            <select x-ref="selectProgram"
                                class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                                <option value="">-- Semua Program --</option>
                                @foreach($programs as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>


                {{-- ============================================================
                     IDENTITAS SISWA
                ============================================================ --}}
                <div class="space-y-4">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2">Identitas Siswa</h3>

                    {{-- Pilih Murid (TomSelect via x-ref) --}}
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Murid</label>
                        <select name="student_id" x-ref="selectMurid"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">
                            <option value="">-- Pilih Murid --</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Gunakan filter Unit / Kelas di atas untuk mempersempit pilihan murid.
                        </p>
                    </div>

                    {{-- Hidden inputs for auto-filled data --}}
                    <input type="hidden" name="program" x-model="program">
                    <input type="hidden" name="level_period" x-model="levelPeriod">
                    {{-- Generate report name automatically if requested, or keep it as hidden for required validation --}}
                    <input type="hidden" name="name" x-bind:value="`Raport ${program} - ${students.find(s => s.user == studentId)?.name || ''}`">
                </div>

                {{-- ============================================================
                     PETUGAS / PENGAJAR
                ============================================================ --}}
                <div class="space-y-4">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2">Petugas</h3>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Pengajar</label>
                        <select name="teacher" x-ref="selectTeacher"
                            class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">
                            <option value="">-- Pilih Pengajar --</option>
                        </select>
                    </div>
                </div>


                {{-- ============================================================
                     A. KOMPETENSI UTAMA
                ============================================================ --}}
                <div class="md:col-span-2 space-y-4 pt-4">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2">A. Kompetensi Utama (Skor 1-4)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl">
                        @foreach([
                            'concept'       => 'Penguasaan Konsep Utama',
                            'concentration' => 'Ketahanan Konsentrasi',
                            'accuracy'      => 'Ketepatan & Kecepatan',
                            'independence'  => 'Kemandirian Belajar'
                        ] as $key => $label)
                        <div class="flex gap-4">
                            <div class="w-24">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Skor</label>
                                <select name="score_{{ $key }}" class="border border-gray-300 rounded-lg px-2 py-1 w-full outline-none">
                                    @for($i=1; $i<=4; $i++)
                                        <option value="{{ $i }}" {{ old('score_'.$key, $items->{'score_'.$key} ?? 4) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Catatan {{ $label }}</label>
                                <input type="text" name="note_{{ $key }}" value="{{ old('note_'.$key, $items->{'note_'.$key} ?? '') }}"
                                    class="border border-gray-300 rounded-lg px-3 py-1 w-full outline-none focus:border-orange-500">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ============================================================
                     B. DESKRIPSI PERKEMBANGAN
                ============================================================ --}}
                <div class="md:col-span-2 space-y-4 pt-4">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2">B. Deskripsi Perkembangan</h3>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach([
                            'strength'        => 'Kekuatan Siswa',
                            'progress_period' => 'Perkembangan Periode Ini',
                            'improvement'     => 'Hal yang Perlu Ditingkatkan'
                        ] as $key => $label)
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">{{ $label }}</label>
                            <textarea name="{{ $key }}" rows="2"
                                class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">{{ old($key, $items->$key ?? '') }}</textarea>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ============================================================
                     C. RINGKASAN & REKOMENDASI
                ============================================================ --}}
                <div class="md:col-span-2 space-y-4 pt-4">
                    <h3 class="font-bold text-orange-600 border-l-4 border-orange-500 pl-2">C. Ringkasan & Rekomendasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Kategori Akhir</label>
                            <select name="category" class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">
                                @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Perlu Pendampingan Khusus'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $items->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Rekomendasi Utama</label>
                            <select name="recommendation" class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">
                                @foreach(['Lanjut Level', 'Perlu Penguatan Materi', 'Siap Naik Level', 'Perlu Evaluasi Khusus'] as $rec)
                                    <option value="{{ $rec }}" {{ old('recommendation', $items->recommendation ?? '') == $rec ? 'selected' : '' }}>{{ $rec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Catatan Rekomendasi</label>
                            <textarea name="recommendation_note" rows="2"
                                class="border border-gray-300 rounded-xl px-3 py-2 w-full focus:ring-2 focus:ring-orange-500 outline-none">{{ old('recommendation_note', $items->recommendation_note ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('dashboard.raport.index') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-2xl hover:bg-gray-300 transition">Batal</a>
                <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-8 rounded-2xl hover:bg-orange-600 shadow-lg transition transform active:scale-95">
                    Simpan Raport
                </button>
            </div>
        </form>
    </div>
@endsection
