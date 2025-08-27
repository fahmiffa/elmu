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
        $request->validate([
            'name'    => ['required', 'regex:/^\S*$/'],
            'addr'    => 'required',
            'kelas'   => 'array',
            'kelas.*' => 'exists:kelas,id',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
            'pic.required'  => 'PIC wajib diisi.',
            'addr.required' => 'Alamat wajib diisi.',
            'hp.required'   => 'Nomor HP wajib diisi.',
            'regex' => 'Tidak boleh memakai spasi'
        ]);

        $item       = new Unit;
        $item->name = $request->name;
        $item->addr = $request->addr;
        $item->save();

        $class = $request->kelas;
        for ($i = 0; $i < count($class); $i++) {
            $kelas           = new UnitKelas;
            $kelas->kelas_id = $class[$i];
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
        $request->validate([
            'name'    => ['required', 'regex:/^\S*$/'],
            'addr'    => 'required',
            'kelas'   => 'array',
            'kelas.*' => 'exists:kelas,id',
        ]);

        $unit->update($request->only('name', 'addr', 'center'));

        // Sync kelas
        $kelas = $request->input('kelas', []);
        $unit->kelas()->sync($kelas);

        return redirect()->route('dashboard.master.unit.index')
            ->with('success', 'Data unit berhasil diperbarui.');
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
