<?php
namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Schedules_students;
use DB;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Head::has('jadwal')->with('jadwal', 'murid')->get();
        return view('home.schedule.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Jadwal";
        $murid  = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'units.jadwal', 'programs:id,name', 'class:id,name')
            ->whereHas('bill', function ($q) {
                $q->where('first', 1)
                    ->where('status', 1);
            })
            ->whereHas('units.jadwal')
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

        return view('home.schedule.form', compact('action', 'murid', 'unit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'murid'    => 'required',
            'murid.*'  => 'required',
            'jadwal'   => 'required',
            'jadwal.*' => 'required',
            'unit'     => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        $jadwal = $request->jadwal;
        $murid  = $request->murid;

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($murid); $i++) {
                $head = Head::where('id', $murid[$i])->first();

                for ($x = 0; $x < count($jadwal); $x++) {
                    $students                    = new Schedules_students;
                    $students->head              = $head->id;
                    $students->student_id        = $head->students;
                    $students->unit_schedules_id = $jadwal[$x];
                    $students->save();
                }
            }

            DB::commit();
            return redirect()->route('dashboard.jadwal.index')->with('status', 'Input Jadwal berhasil');
        } catch (\Exception $e) {
            DB::rollback();
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
    public function edit($id)
    {
        $items  = Head::where('id', $id)->with('jadwal', 'murid')->first();
        $action = "Edit Jadwal";
        $murid  = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'units.jadwal', 'programs:id,name', 'class:id,name')
            ->whereHas('bill', function ($q) {
                $q->where('first', 1)
                    ->where('status', 1);
            })
            ->where('id', $id)
            ->whereHas('units.jadwal')
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
            'unit'   => $items->unit,
            'murid'  => $items->murid->pluck('id')->toArray(),
            'jadwal' => $items->jadwal->pluck('id')->toArray(),
        ];

        return view('home.schedule.form', compact('action', 'murid', 'unit', 'items', 'selected'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'murid'    => 'required',
            'murid.*'  => 'required',
            'jadwal'   => 'required',
            'jadwal.*' => 'required',
            'unit'     => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        $items = Head::where('id', $id)->with('jadwal', 'murid')->first();
        $jadwal = $request->jadwal;
        $murid  = $request->murid;

        DB::beginTransaction();
        try {
            
            for ($i = 0; $i < count($murid); $i++) {
                $head = Head::where('id', $murid[$i])->first();
                Schedules_students::where('head',$head->id)->where('student_id',$head->students)->delete();
                for ($x = 0; $x < count($jadwal); $x++) {
                    $students                    = new Schedules_students;
                    $students->head              = $head->id;
                    $students->student_id        = $head->students;
                    $students->unit_schedules_id = $jadwal[$x];
                    $students->save();
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
    public function hapus(Request $request, $id)
    {
        Schedules_students::where('head',$id)->whereIn('unit_schedules_id',$request->par)->delete();
         return redirect()->route('dashboard.jadwal.index')->with('status', 'Hapus Jadwal berhasil');
    }
    
}
