<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use App\Models\Student;
use App\Models\Schedules_students;
use App\Models\UnitSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermitController extends Controller
{
    public function index()
    {
        $query = Permit::with(['student', 'scheduleStudent.sch', 'unitSchedule'])->latest();
        
        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $query->whereHas('student.reg', function($q) use ($unitIds) {
                $q->whereIn('unit', $unitIds);
            });
        }
        
        $permits = $query->get();
        return view('permit.index', compact('permits'));
    }

    public function create()
    {
        $query = Student::orderBy('name');
        
        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $query->whereHas('reg', function($q) use ($unitIds) {
                $q->whereIn('unit', $unitIds)->where('done',0);
            });
        }
        
        $students = $query->get(); 
        // Awalnya kosong, akan diisi via AJAX saat siswa dipilih
        $unitSchedules = collect(); 
        return view('permit.create', compact('students', 'unitSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'             => 'required|date|after_or_equal:today',
            // 'student_id'          => 'required|exists:students,id',
            'schedule_student_id' => 'required|exists:schedules_students,id',
            'unit_schedules_id'   => 'required|exists:unit_schedules,id',
            'new_date'            => 'required|date|after_or_equal:tanggal',
            'why'                 => 'required|string',
            'student_id'          => 'required|exists:head,students,done,0'
        ], [
            'tanggal.required'             => 'Tanggal asal wajib diisi.',
            'tanggal.after_or_equal'       => 'Tanggal asal tidak boleh kurang dari hari ini.',
            'new_date.required'            => 'Tanggal pengganti wajib diisi.',
            'new_date.after_or_equal'      => 'Tanggal pengganti tidak boleh kurang dari tanggal asal.',
            'schedule_student_id.required' => 'Sesi jadwal asal wajib dipilih.',
            'unit_schedules_id.required'   => 'Sesi jadwal tujuan wajib dipilih.',
        ]);

        // Validasi: tanggal ASAL harus sesuai hari sesi ASAL
        $errAsal = $this->validateTanggalHariAsal($request->tanggal, $request->schedule_student_id);
        if ($errAsal) {
            return back()->withErrors(['tanggal' => $errAsal])->withInput();
        }

        // Validasi: tanggal PENGGANTI harus sesuai hari sesi TUJUAN
        $errBaru = $this->validateTanggalHariBaru($request->new_date, $request->unit_schedules_id);
        if ($errBaru) {
            return back()->withErrors(['new_date' => $errBaru])->withInput();
        }

        Permit::create($request->only([
            'tanggal', 'student_id', 'schedule_student_id',
            'unit_schedules_id', 'new_date', 'why',
        ]));

        return redirect()->route('dashboard.presensi.index')
            ->with('status', 'Data Presensi Ganti Jadwal berhasil ditambahkan.');
    }

    public function show(Permit $presensi)
    {
    }

    public function edit(Permit $presensi)
    {
        $query = Student::orderBy('name');
        
        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $query->whereHas('reg', function($q) use ($unitIds) {
                $q->whereIn('unit', $unitIds);
            });
        }
        
        $students = $query->get();
        $schedules = Schedules_students::with('sch')->where('student_id', $presensi->student_id)->get();
        
        $student = Student::with(['reg' => fn($q) => $q->where('done', 0)])->find($presensi->student_id);
        $unit_id = $student?->reg->where('done', 0)->first()?->unit;
        
        $unitSchedules = $unit_id 
            ? UnitSchedule::where('unit_id', $unit_id)->orderBy('parse')->orderBy('start')->get()
            : UnitSchedule::orderBy('parse')->orderBy('start')->get();
            
        return view('permit.edit', compact('presensi', 'students', 'schedules', 'unitSchedules'));
    }

    public function update(Request $request, Permit $presensi)
    {
        $request->validate([
            'tanggal'             => 'required|date|after_or_equal:today',
            'student_id'          => 'required|exists:students,id',
            'schedule_student_id' => 'required|exists:schedules_students,id',
            'unit_schedules_id'   => 'required|exists:unit_schedules,id',
            'new_date'            => 'required|date|after_or_equal:tanggal',
            'why'                 => 'required|string',
        ], [
            'tanggal.required'             => 'Tanggal asal wajib diisi.',
            'tanggal.after_or_equal'       => 'Tanggal asal tidak boleh kurang dari hari ini.',
            'new_date.required'            => 'Tanggal pengganti wajib diisi.',
            'new_date.after_or_equal'      => 'Tanggal pengganti tidak boleh kurang dari tanggal asal.',
            'schedule_student_id.required' => 'Sesi jadwal asal wajib dipilih.',
            'unit_schedules_id.required'   => 'Sesi jadwal tujuan wajib dipilih.',
        ]);

        // Validasi: tanggal ASAL harus sesuai hari sesi ASAL
        $errAsal = $this->validateTanggalHariAsal($request->tanggal, $request->schedule_student_id);
        if ($errAsal) {
            return back()->withErrors(['tanggal' => $errAsal])->withInput();
        }

        // Validasi: tanggal PENGGANTI harus sesuai hari sesi TUJUAN
        $errBaru = $this->validateTanggalHariBaru($request->new_date, $request->unit_schedules_id);
        if ($errBaru) {
            return back()->withErrors(['new_date' => $errBaru])->withInput();
        }

        $presensi->update($request->only([
            'tanggal', 'student_id', 'schedule_student_id',
            'unit_schedules_id', 'new_date', 'why',
        ]));

        return redirect()->route('dashboard.presensi.index')
            ->with('status', 'Data Presensi Ganti Jadwal berhasil diubah.');
    }

    public function destroy(Permit $presensi)
    {
        $presensi->delete();
        return redirect()->route('dashboard.presensi.index')->with('status', 'Data Presensi berhasil dihapus.');
    }

    public function getSchedule(Request $request)
    {
        $student_id = $request->student_id;
        
        // 1. Jadwal Asal (berdasarkan tabel pivot schedules_students)
        $schedules = Schedules_students::with('sch')->where('student_id', $student_id)->get();
        
        $options = '<option value="">Pilih Sesi Jadwal</option>';
        foreach($schedules as $sch) {
            foreach($sch->sch as $session) {
                $hari = $session->parse ?? 'Jadwal';
                $name = $session->name ?? 'Sesi';
                $options .= '<option value="'.$sch->id.'" data-hari="'.strtolower($hari).'">'.$hari.' - '.$name.' ('.$session->start_time.' s/d '.$session->end_time.')</option>';
            }
        }

        // 2. Jadwal Tujuan (difilter berdasarkan unit_id siswa)
        $student = \App\Models\Student::with(['reg' => fn($q) => $q->where('done', 0)])->find($student_id);
        $unit_id = $student?->reg->where('done', 0)->first()?->unit;
        
        $targetOptions = '<option value="">Pilih Ke Sesi Jadwal</option>';
        if ($unit_id) {
            $unitSchedules = \App\Models\UnitSchedule::where('unit_id', $unit_id)
                                ->orderBy('parse')->orderBy('start')->get();
            foreach($unitSchedules as $sch) {
                $hari = $sch->parse ?? 'Jadwal';
                $name = $sch->name ?? 'Sesi';
                $targetOptions .= '<option value="'.$sch->id.'" data-hari="'.strtolower($hari).'">'.$hari.' - '.$name.' ('.$sch->start_time.' s/d '.$sch->end_time.')</option>';
            }
        }
        
        return response()->json([
            'options' => $options,
            'target_options' => $targetOptions
        ]);
    }

    /**
     * Mengembalikan info siswa (nama, program, unit) untuk ditampilkan di form
     */
    public function getStudentInfo(Request $request)
    {
        $student = \App\Models\Student::with([
            'reg' => fn($q) => $q->where('done', 0)->with(['units', 'programs'])
        ])->find($request->student_id);

        if (!$student) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        $head    = $student->reg->where('done', 0)->first();
        $unit    = $head?->units?->name ?? '-';
        $program = $head?->programs?->name ?? '-';

        return response()->json([
            'name'    => $student->name,
            'unit'    => $unit,
            'program' => $program,
        ]);
    }

    /**
     * Validasi tanggal ASAL vs hari di sesi ASAL (schedule_student_id)
     */
    private function validateTanggalHariAsal($tanggal, $schedule_student_id)
    {
        $jadwal_siswa = \App\Models\Schedules_students::with('sch')->find($schedule_student_id);
        if ($jadwal_siswa && $jadwal_siswa->sch->count() > 0) {
            $session     = $jadwal_siswa->sch->first();
            $hariJadwal  = strtolower($session->parse ?? '');
            if ($hariJadwal) {
                $hariTanggal = $this->getDayName(\Carbon\Carbon::parse($tanggal)->dayOfWeek);
                if ($hariJadwal !== $hariTanggal) {
                    return 'Tanggal asal harus hari ' . ucfirst($hariJadwal) . ' (sesuai sesi jadwal asal yang dipilih).';
                }
            }
        }
        return null;
    }

    /**
     * Validasi tanggal PENGGANTI vs hari di sesi TUJUAN (unit_schedules_id)
     */
    private function validateTanggalHariBaru($new_date, $unit_schedules_id)
    {
        $unitSch = \App\Models\UnitSchedule::find($unit_schedules_id);
        if ($unitSch) {
            $hariJadwal = strtolower($unitSch->parse ?? '');
            if ($hariJadwal) {
                $hariTanggal = $this->getDayName(\Carbon\Carbon::parse($new_date)->dayOfWeek);
                if ($hariJadwal !== $hariTanggal) {
                    return 'Tanggal pengganti harus hari ' . ucfirst($hariJadwal) . ' (sesuai sesi tujuan yang dipilih).';
                }
            }
        }
        return null;
    }

    private function getDayName(int $dayOfWeek): string
    {
        return [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ][$dayOfWeek] ?? '';
    }
}
