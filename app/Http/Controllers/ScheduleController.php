<?php
namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Schedule;
use App\Models\Schedules_students;
use App\Models\Schedule_date;
use App\Models\Schedule_meet;
use DB;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Schedule::with('waktu', 'meet.waktu', 'murid', 'units', 'programs', 'class')->get();
        return view('home.schedule.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Jadwal";
        $murid  = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'programs:id,name', 'class:id,name')
            ->whereHas('bill', function ($q) {
                $q->where('first', 1)
                    ->where('status', 1);
            })
            ->DoesntHave('jadwal')
            ->get();

        $unit = $murid->pluck('units')
            ->unique('id')
            ->map(function ($unit) {
                return [
                    'id'   => $unit->id,
                    'name' => $unit->name,
                ];
            })
            ->values();

        return view('home.schedule.form', compact('action', 'murid', 'unit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'murid'   => 'required',
            'murid.*' => 'required',
            'unit'    => 'required',
            'program' => 'required',
            'kelas'   => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $meet = $request->pertemuan;

        DB::beginTransaction();
        try {
            $sch          = new Schedule;
            $sch->unit    = $request->unit;
            $sch->kelas   = $request->kelas;
            $sch->program = $request->program;
            $sch->save();

            $murid = $request->murid;
            for ($i = 0; $i < count($murid); $i++) {
                $head = Head::where('id',$murid[$i])->first();
                $students              = new Schedules_students;
                $students->head        = $head->id;
                $students->student_id  = $head->students;
                $students->schedule_id = $sch->id;
                $students->save();
            }

            $meet = $request->pertemuan;

            for ($i = 0; $i < count($meet); $i++) {
                $sch_meet              = new Schedule_meet;
                $sch_meet->schedule_id = $sch->id;
                $sch_meet->name        = $meet[$i]['nama'];
                $sch_meet->save();

                $date = $meet[$i]['tanggal'];
                for ($x = 0; $x < count($date); $x++) {
                    $sch_date                   = new Schedule_date;
                    $sch_date->schedule_meet_id = $sch_meet->id;
                    $sch_date->waktu            = $date[$x];
                    $sch_date->save();
                }
            }

            DB::commit();
            return redirect()->route('dashboard.jadwal.index')->with('status', 'Input Jadwal berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return back()->with('err', 'Terjadi Kesalahan Proses Input');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $jadwal)
    {
        $action = "Edit Jadwal";
        $items  = $jadwal->load('class', 'units', 'programs', 'murid');
        foreach ($items->meet as $val) {

            foreach ($val->waktu as $waktu) {
                $tanggalList[] = [
                    'tanggal' => \Carbon\Carbon::parse($waktu->tanggal)->format('Y-m-d\TH:i'),
                ];
            }

            $da[] = [
                'nama'        => $val->name,
                'tanggalList' => $tanggalList,
            ];
        }

        $murid = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'programs:id,name', 'class:id,name')
            ->whereHas('bill', function ($q) {
                $q->where('first', 1)
                    ->where('status', 1);
            })
        // ->DoesntHave('jadwal')
            ->get();

        $unit = $murid->pluck('units')
            ->unique('id')
            ->map(function ($unit) {
                return [
                    'id'   => $unit->id,
                    'name' => $unit->name,
                ];
            })
            ->values();

        $selected = [
            'unit'    => optional($jadwal->units)->id,
            'kelas'   => optional($jadwal->class)->id,
            'program' => optional($jadwal->programs)->id,
            'murid'   => $items->siswa->pluck('head')->toArray(),
        ];

        return view('home.schedule.form', compact('action', 'items', 'da', 'unit', 'murid', 'selected'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $jadwal)
    {

        $request->validate([
            'murid'   => 'required',
            'murid.*' => 'required',
            'unit'    => 'required',
            'program' => 'required',
            'kelas'   => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $meet = $request->pertemuan;

        DB::beginTransaction();
        try {

            $sch          = $jadwal;
            $sch->unit    = $request->unit;
            $sch->kelas   = $request->kelas;
            $sch->program = $request->program;
            $sch->save();

            Schedules_students::where('schedule_id', $jadwal->id)->delete();
            $murid = $request->murid;
            for ($i = 0; $i < count($murid); $i++) {
                $students              = new Schedules_students;
                $students->student_id  = $murid[$i];
                $students->schedule_id = $sch->id;
                $students->save();
            }

            Schedule_date::whereIn('schedule_meet_id', $jadwal->meet->pluck('id')->toArray())->delete();
            Schedule_meet::where('schedule_id', $jadwal->id)->delete();
            $meet = $request->pertemuan;
            for ($i = 0; $i < count($meet); $i++) {
                $sch_meet              = new Schedule_meet;
                $sch_meet->schedule_id = $jadwal->id;
                $sch_meet->name        = $meet[$i]['nama'];
                $sch_meet->save();

                $date = $meet[$i]['tanggal'];
                for ($x = 0; $x < count($date); $x++) {
                    $sch_date                   = new Schedule_date;
                    $sch_date->schedule_meet_id = $sch_meet->id;
                    $sch_date->waktu            = $date[$x];
                    $sch_date->save();
                }
            }

            DB::commit();
            return redirect()->route('dashboard.jadwal.index')->with('status', 'Update Jadwal berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return back()->with('err', 'Terjadi Kesalahan Proses Input');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
