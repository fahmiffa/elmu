<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Zone;
use App\Models\Zone_units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'hp'     => 'required',
            'pic'    => 'required',
            'unit'   => 'required|array',
            'unit.*' => 'exists:units,id',
        ], [
            'required' => 'Field wajib diisi.',
            'unit.required' => 'Pilih setidaknya satu unit.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Zona berhasil disimpan!']);
        }

        return redirect()->route('dashboard.master.zone.index')->with('status', 'Zona berhasil disimpan!');
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
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'hp'     => 'required',
            'pic'    => 'required',
            'unit'   => 'required|array',
            'unit.*' => 'exists:units,id',
        ], [
            'required' => 'Field wajib diisi.',
            'unit.required' => 'Pilih setidaknya satu unit.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $zone->update($request->only('name', 'hp', 'pic'));

        $unit = $request->input('unit', []);
        $zone->unit()->sync($unit);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Data Zona berhasil diperbarui.']);
        }

        return redirect()->route('dashboard.master.zone.index')
            ->with('status', 'Data Zona berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zone $zone)
    {
        $zone->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Zona berhasil dihapus!']);
        }

        return redirect()->route('dashboard.master.zone.index')->with('status', 'Zona berhasil dihapus!');
    }
}
