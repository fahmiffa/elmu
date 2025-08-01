<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Unit::all();
        return view('unit.index', compact('items'));
    }

    public function create()
    {
        $action = "Tambah Unit";
        return view('unit.form', compact('action'));
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
        return view('unit.form', compact('items', 'action'));
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
