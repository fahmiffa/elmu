<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Kelas;
use App\Models\Schedules_students;
use App\Models\Unit;
use App\Models\UnitKelas;
use App\Models\UnitSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Unit::with('kelas')->latest()->get();
        return view('master.unit.index', compact('items'));
    }

    public function create()
    {
        $action = "Tambah Unit";
        $kelas  = Kelas::latest()->get();
        return view('master.unit.form', compact('action', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => ['required', 'regex:/^\S*$/'],
            'addr'    => 'required',
            'kelas'   => 'array',
            'kelas.*' => 'exists:kelas,id',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
            'addr.required' => 'Alamat wajib diisi.',
            'regex'         => 'Tidak boleh memakai spasi',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item       = new Unit;
        $item->name = $request->name;
        $item->addr = $request->addr;
        $item->save();

        $class = $request->kelas;
        if ($class) {
            for ($i = 0; $i < count($class); $i++) {
                $kelas           = new UnitKelas;
                $kelas->kelas_id = $class[$i];
                $kelas->unit_id  = $item->id;
                $kelas->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Unit berhasil disimpan!']);
        }

        return redirect()->route('dashboard.master.unit.index')->with('status', 'Unit berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $items  = $unit->load('kelas');
        $action = "Edit Unit";
        $kelas  = Kelas::latest()->get();
        return view('master.unit.form', compact('items', 'action', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name'    => ['required', 'regex:/^\S*$/'],
            'addr'    => 'required',
            'kelas'   => 'array',
            'kelas.*' => 'exists:kelas,id',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
            'addr.required' => 'Alamat wajib diisi.',
            'regex'         => 'Tidak boleh memakai spasi',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $unit->update($request->only('name', 'addr', 'center'));

        // Sync kelas
        $kelas = $request->input('kelas', []);
        $unit->kelas()->sync($kelas);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Data unit berhasil diperbarui.']);
        }

        return redirect()->route('dashboard.master.unit.index')
            ->with('status', 'Data unit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Cek apakah unit sudah digunakan di data murid (Head)
        $used = Head::where('unit', $unit->id)->exists();

        if ($used) {
            $message = 'Unit tidak dapat dihapus karena sudah digunakan oleh murid.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 422);
            }
            return back()->with('err', $message);
        }

        try {
            $unit->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Unit berhasil dihapus!']);
            }

            return redirect()->route('dashboard.master.unit.index')->with('status', 'Unit berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus unit: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }

    public function jadwal()
    {
        $items = Unit::has('jadwal')->with('jadwal')->latest()->get();
        return view('master.unit.jadwal.index', compact('items'));
    }

    public function jadwalCreate()
    {
        $action = "Tambah Jadwal Unit";
        $kelas  = Kelas::latest()->get();
        $unit   = Unit::latest()->get();
        return view('master.unit.jadwal.form', compact('action', 'kelas', 'unit'));
    }

    public function jadwalEdit($id)
    {
        $action = "Edit Jadwal Unit";
        $items  = Unit::with('jadwal')->findOrFail($id);

        $jadwals = $items->jadwal->map(function ($jadwal) {
            return [
                'id'         => $jadwal->id,
                'name'       => $jadwal->name,
                'hari'       => $jadwal->day,
                'start_time' => date('H:i', strtotime($jadwal->start)), // pastikan format HH:mm
                'end_time'   => date('H:i', strtotime($jadwal->end)),   // pastikan format HH:mm
            ];
        });

        $unit = Unit::has('jadwal')->with('jadwal')->latest()->get();
        return view('master.unit.jadwal.form', compact('action', 'unit', 'items', 'jadwals'));
    }

    public function jadwalUpdate(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'unit'                => ['required', 'exists:units,id'],
            'jadwal'              => ['required', 'array', 'min:1'],
            'jadwal.*.name'       => ['required'],
            'jadwal.*.hari'       => ['required', 'in:1,2,3,4,5,6,7'],
            'jadwal.*.start_time' => ['required', 'date_format:H:i'],
            'jadwal.*.end_time'   => ['required', 'date_format:H:i'],
        ])->after(function ($validator) use ($request) {
            $byDay = [];

            foreach ($request->jadwal as $index => $jadwal) {
                $day   = $jadwal['hari'];
                $start = strtotime($jadwal['start_time']);
                $end   = strtotime($jadwal['end_time']);

                if ($start >= $end) {
                    $validator->errors()->add("jadwal.$index.end_time", 'Waktu selesai harus lebih dari waktu mulai.');
                    continue;
                }

                if (! isset($byDay[$day])) {
                    $byDay[$day] = [];
                }

                foreach ($byDay[$day] as $range) {
                    if ($start < $range['end'] && $end > $range['start']) {
                        $validator->errors()->add("jadwal.$index.start_time", "Waktu jadwal ini bentrok dengan jadwal lain di hari yang sama.");
                        break;
                    }
                }

                $byDay[$day][] = ['start' => $start, 'end' => $end];
            }
        })->validate();

        $jadwalId = UnitSchedule::where('unit_id', $id)->pluck('id')->toArray();

        $new  = $ids  = array_column($request->jadwal, 'id');
        $diff = array_diff($jadwalId, $new);

        if (count($diff) > 0) {
            $be = UnitSchedule::whereIn('id', $diff)->has('set')->exists();
            if ($be) {
                return back()->with('err', 'Jadwal sudah di pakai, tidak bisa di hapus');
            } else {
                UnitSchedule::whereIn('id', $diff)->delete();
            }
        }

        // Simpan ulang semua jadwal baru
        foreach ($request->jadwal as $jadwal) {
            $be  = UnitSchedule::where('id', $jadwal['id']);
            $sch          = $be->exists() ? $be->first() : new UnitSchedule;
            $sch->unit_id = $request->unit;
            $sch->name    = $jadwal['name'];
            $sch->day     = $jadwal['hari'];
            $sch->parse   = $this->convertHari($jadwal['hari']);
            $sch->start   = $jadwal['start_time'];
            $sch->end     = $jadwal['end_time'];
            $sch->save();
        }

        return redirect()->route('dashboard.master.jadwal.index')
            ->with('success', 'Data Jadwal Unit berhasil diperbarui.');
    }

    public function jadwalStore(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'unit'                => ['required', 'exists:units,id'],
            'jadwal'              => ['required', 'array', 'min:1'],
            'jadwal.*.name'       => ['required'],
            'jadwal.*.hari'       => ['required', 'in:1,2,3,4,5,6,7'],
            'jadwal.*.start_time' => ['required', 'date_format:H:i'],
            'jadwal.*.end_time'   => ['required', 'date_format:H:i'],
        ], [
            'unit.required'                   => 'Unit wajib diisi.',
            'unit.exists'                     => 'Unit tidak ditemukan.',
            'jadwal.required'                 => 'Jadwal wajib diisi.',
            'jadwal.array'                    => 'Format jadwal harus berupa array.',
            'jadwal.min'                      => 'Minimal ada satu jadwal.',
            'jadwal.*.name.required'          => 'Nama jadwal wajib diisi.',
            'jadwal.*.hari.required'          => 'Hari wajib diisi.',
            'jadwal.*.hari.in'                => 'Hari harus antara 1 sampai 7.',
            'jadwal.*.start_time.required'    => 'Waktu mulai wajib diisi.',
            'jadwal.*.start_time.date_format' => 'Format waktu mulai harus HH:mm.',
            'jadwal.*.end_time.required'      => 'Waktu selesai wajib diisi.',
            'jadwal.*.end_time.date_format'   => 'Format waktu selesai harus HH:mm.',
        ])->after(function ($validator) use ($request) {
            $byDay = [];
            if ($request->jadwal) {
                foreach ($request->jadwal as $index => $jadwal) {
                    $day   = $jadwal['hari'];
                    $start = strtotime($jadwal['start_time']);
                    $end   = strtotime($jadwal['end_time']);

                    if ($start >= $end) {
                        $validator->errors()->add("jadwal.$index.end_time", 'Waktu selesai harus lebih dari waktu mulai.');
                        continue;
                    }

                    if (! isset($byDay[$day])) {
                        $byDay[$day] = [];
                    }

                    foreach ($byDay[$day] as $i => $range) {
                        $existingStart = $range['start'];
                        $existingEnd   = $range['end'];

                        $overlap = $start < $existingEnd && $end > $existingStart;
                        if ($overlap) {
                            $validator->errors()->add("jadwal.$index.start_time", "Waktu sesi ini bentrok dengan sesi lain di hari yang sama.");
                            break;
                        }
                    }

                    $byDay[$day][] = ['start' => $start, 'end' => $end];
                }
            }
        })->validate();

        foreach ($request->jadwal as $jadwal) {
            $sch          = new UnitSchedule;
            $sch->unit_id = $request->unit;
            $sch->name    = $jadwal['name'];
            $sch->day     = $jadwal['hari'];
            $sch->parse   = $this->convertHari($jadwal['hari']);
            $sch->start   = $jadwal['start_time'];
            $sch->end     = $jadwal['end_time'];
            $sch->save();
        }

        return redirect()->route('dashboard.master.jadwal.index')
            ->with('success', 'Data Jadwal Unit berhasil diinput.');
    }

    private function convertHari($number)
    {
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        // Cast ke int untuk jaga-jaga
        $num = (int) $number;

        return $hari[$num] ?? null;
    }

    public function jadwalDestroy(Request $request, $id)
    {
        try {
            // Cek apakah jadwal unit ini sudah digunakan oleh murid
            $scheduleIds = UnitSchedule::where('unit_id', $id)->pluck('id');
            $used = Schedules_students::whereIn('unit_schedules_id', $scheduleIds)->exists();

            if ($used) {
                $message = 'Jadwal tidak dapat dihapus karena sudah digunakan oleh murid.';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 422);
                }
                return back()->with('err', $message);
            }

            UnitSchedule::where('unit_id', $id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Data Jadwal Unit berhasil dihapus.']);
            }

            return redirect()->route('dashboard.master.jadwal.index')
                ->with('success', 'Data Jadwal Unit berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus jadwal unit: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus jadwal unit: ' . $e->getMessage());
        }
    }
}
