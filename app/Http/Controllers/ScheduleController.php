<?php
namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Schedule;
use App\Models\Schedule_date;
use App\Models\Schedule_meet;
use App\Models\Teach;
use DB;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Schedule::with('waktu', 'meet.waktu', 'reg.murid', 'reg.units')->get();
        return view('home.schedule.index', compact('items'));
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
        })->DoesntHave('jadwal')->get();
        return view('home.schedule.form', compact('action', 'guru', 'murid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'murid' => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $meet = $request->pertemuan;

        DB::beginTransaction();
        try {
            $sch       = new Schedule;
            $sch->head = $request->murid;
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
        $action = "Edit Jadwal " . $jadwal->reg->murid->name;
        $items  = $jadwal;
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
        return view('home.schedule.form', compact('action', 'items', 'da'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $jadwal)
    {

        $meet = $request->pertemuan;

        DB::beginTransaction();
        try {

            Schedule_date::whereIn('schedule_meet_id',$jadwal->meet->pluck('id')->toArray())->delete();
            Schedule_meet::where('schedule_id',$jadwal->id)->delete();

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
