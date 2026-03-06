<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Schedules_students;
use App\Services\Firebase\FirebaseMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Head::has('jadwal')->with('jadwal', 'murid', 'class', 'units', 'programs');
        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $query->whereIn('unit', $unitIds);
        }
        $items = $query->get()->map(function ($item) {
            // Because jadwal is a many-to-many through schedules_students, 
            // we can get the program name from the pivot table if we load it properly,
            // but the user wants a simple way to see it.
            // Let's just pass it or ensure schedules_students model is used.
            $firstSchedule = Schedules_students::where('head', $item->id)->with('program')->first();
            $item->present_program = $firstSchedule ? $firstSchedule->program : null;
            return $item;
        });

        return view('home.schedule.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Jadwal";
        $murid  = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'units.jadwal', 'programs:id,name', 'class:id,name');

        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $murid->whereIn('unit', $unitIds);
        }

        $murid = $murid->get();

        $unit = $murid->pluck('units')
            ->unique('id')
            ->map(function ($unit) {
                if (!$unit) return null;
                return [
                    'id'   => $unit->id,
                    'name' => $unit->name,
                ];
            })
            ->filter()
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
            'program'  => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        $jadwal = $request->jadwal;
        $murid  = $request->murid;
        $program = $request->program;

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($murid); $i++) {
                $head = Head::where('id', $murid[$i])->first();

                for ($x = 0; $x < count($jadwal); $x++) {
                    $students                    = new Schedules_students;
                    $students->head              = $head->id;
                    $students->student_id        = $head->students;
                    $students->unit_schedules_id = $jadwal[$x];
                    $students->program_id        = $program;
                    $students->save();

                    $this->send($students, "Jadwal Anda telah terbit");
                }
            }

            DB::commit();
            return redirect()->route('dashboard.jadwal.index')->with('status', 'Input Jadwal berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('err', 'Terjadi Kesalahan Proses Input');
        }
    }

    private function send($students, $msg)
    {
        $fcm     = $students->reg?->murid?->users?->fcm;
        if ($fcm) {
            $message = [
                "message" => [
                    "token"        => $fcm,
                    "notification" => [
                        "title" => "Jadwal",
                        "body"  => $msg,
                    ],
                ],
            ];

            FirebaseMessage::sendFCMMessage($message);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $items = Head::where('id', $id)
            ->with('jadwal', 'murid', 'units:id,name', 'units.jadwal', 'programs:id,name', 'class:id,name')
            ->firstOrFail();

        $action = "Edit Jadwal";

        // Query murid & unit filtered by zone
        $query = Head::select('id', 'kelas', 'unit', 'program', 'students')
            ->with('murid:id,name', 'units:id,name', 'units.jadwal', 'programs:id,name', 'class:id,name');

        if (Auth::user()->role == 4) {
            $unitIds = DB::table('zone_units')->where('zone_id', Auth::user()->zone_id)->pluck('unit_id');
            $query->whereIn('unit', $unitIds);
        }
        $murid = $query->get();

        $unit = $murid->pluck('units')
            ->unique('id')
            ->map(function ($u) {
                if (!$u) return null;
                return [
                    'id'   => $u->id,
                    'name' => $u->name,
                ];
            })
            ->filter()
            ->values();

        $firstSchedule = Schedules_students::where('head', $items->id)->first();

        $selected = [
            'unit'    => $items->unit,
            'program' => $firstSchedule ? $firstSchedule->program_id : null,
            'murid'   => [$items->id],
            'jadwal'  => $items->jadwal->pluck('id')->toArray(),
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
            'program'  => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        $items  = Head::where('id', $id)->with('jadwal', 'murid')->first();
        $jadwal = $request->jadwal;
        $murid  = $request->murid;
        $program = $request->program;

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($murid); $i++) {
                $head = Head::where('id', $murid[$i])->first();
                Schedules_students::where('head', $head->id)->where('student_id', $head->students)->delete();
                for ($x = 0; $x < count($jadwal); $x++) {
                    $students                    = new Schedules_students;
                    $students->head              = $head->id;
                    $students->student_id        = $head->students;
                    $students->unit_schedules_id = $jadwal[$x];
                    $students->program_id        = $program;
                    $students->save();
                    $this->send($students, "Jadwal Anda telah diupdate");
                }
            }

            DB::commit();
            return redirect()->route('dashboard.jadwal.index')->with('status', 'Update Jadwal berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('err', 'Terjadi Kesalahan Proses Input');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function hapus(Request $request, $id)
    {
        try {
            Schedules_students::where('head', $id)->whereIn('unit_schedules_id', $request->par)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Hapus Jadwal berhasil']);
            }

            return redirect()->route('dashboard.jadwal.index')->with('status', 'Hapus Jadwal berhasil');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}
