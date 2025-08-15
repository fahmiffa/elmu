<?php
namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Schedule;
use App\Models\Schedule_date;
use App\Models\Schedule_meet;
use App\Models\Teach;
use Illuminate\Http\Request;
use DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Schedule::with('waktu','meet.waktu','reg.murid','guru')->get();
        return view('schedule.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Jadwal";
        $guru   = Teach::select('id', 'name')->latest()->get();
        $murid  = Head::with('murid')->whereDoesntHave('bill', function ($q) {
            $q->where('status', '!=', 1);
        })->get();
        return view('schedule.form', compact('action', 'guru', 'murid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru'  => 'required',
            'murid' => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);
        DB::beginTransaction();
        try {
            $sch           = new Schedule;
            $sch->head     = $request->murid;
            $sch->teach_id = $request->guru;
            $sch->save();

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
            return back()->with('err',$e);
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
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
