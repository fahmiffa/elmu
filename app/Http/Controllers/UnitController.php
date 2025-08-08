<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Unit;
use App\Models\UnitKelas;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Unit::with('kelas.kelasName')->latest()->get();
        return view('unit.index', compact('items'));
    }

    public function create()
    {
        $action = "Tambah Unit";
        $kelas  = Kelas::latest()->get();
        return view('unit.form', compact('action', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'pic'  => 'required',
            'hp'   => 'required',
            'addr' => 'required',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
            'pic.required'  => 'PIC wajib diisi.',
            'addr.required' => 'Alamat wajib diisi.',
            'hp.required'   => 'Nomor HP wajib diisi.',
        ]);

        $item       = new Unit;
        $item->name = $request->name;
        $item->pic  = $request->pic;
        $item->hp   = $request->hp;
        $item->addr = $request->addr;
        $item->save();

        $kelas = $request->kelas;
        for ($i = 0; $i < count($kelas); $i++) {
            $kelas           = new UnitKelas;
            $kelas->kelas_id = $kelas[$i];
            $kelas->unit_id  = $item->id;
            $kelas->save();
        }

        return redirect()->route('dashboard.master.unit.index');
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $items  = $unit;
        $action = "Edit Unit";
        $kelas  = Kelas::latest()->get();
        return view('unit.form', compact('items', 'action', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required',
            'pic'  => 'required',
            'hp'   => 'required',
            'addr' => 'required',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
            'pic.required'  => 'PIC wajib diisi.',
            'addr.required' => 'Alamat wajib diisi.',
            'hp.required'   => 'Nomor HP wajib diisi.',
        ]);

        $item       = $unit;
        $item->name = $request->name;
        $item->pic  = $request->pic;
        $item->hp   = $request->hp;
        $item->addr = $request->addr;
        $item->save();

        $kelas = $request->kelas;
        if (count($kelas) > 0) {
            UnitKelas::where('unit_id', $item->id)->delete();
        }
        for ($i = 0; $i < count($kelas); $i++) {
            $kelasUnit           = new UnitKelas;
            $kelasUnit->kelas_id = $kelas[$i];
            $kelasUnit->unit_id  = $item->id;
            $kelasUnit->save();
        }

        return redirect()->route('dashboard.master.unit.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('dashboard.master.unit.index');
    }
}
