<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Zone;
use App\Models\Zone_units;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Zone::with('unit')->latest()->get();
        return view('master.zone.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Zona";
        $unit   = Unit::latest()->get();
        return view('master.zone.form', compact('action', 'unit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'hp'     => 'required',
            'pic'    => 'required',
            'unit'   => 'required|array',
            'unit.*' => 'exists:units,id',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $item       = new Zone;
        $item->name = $request->name;
        $item->hp   = $request->hp;
        $item->pic  = $request->pic;
        $item->save();

        $unit = $request->unit;
        for ($i = 0; $i < count($unit); $i++) {
            $zones_unit          = new Zone_units;
            $zones_unit->unit_id = $unit[$i];
            $zones_unit->zone_id = $item->id;
            $zones_unit->save();
        }

        return redirect()->route('dashboard.master.zone.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        $items  = $zone->load('unit');
        $action = "Edit Zone";
        $unit   = Unit::latest()->get();
        return view('master.zone.form', compact('items', 'action', 'unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name'   => 'required',
            'hp'     => 'required',
            'pic'    => 'required',
            'unit'   => 'required|array',
            'unit.*' => 'exists:units,id',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $zone->update($request->only('name', 'hp', 'pic'));

        $unit = $request->input('unit', []);
        $zone->unit()->sync($unit);

        return redirect()->route('dashboard.master.zone.index')
            ->with('success', 'Data Zona berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
