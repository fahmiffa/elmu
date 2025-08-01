<?php
namespace App\Http\Controllers;

use App\Models\Teach;
use Illuminate\Http\Request;

class TeachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Teach::all();
        return view('teach.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Guru";
        return view('teach.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'birth' => 'required',
            'hp'    => 'required',
            'addr'  => 'required',
            'study' => 'required',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
        ]);

        $item        = new Teach;
        $item->name  = $request->name;
        $item->birth = $request->birth;
        $item->hp    = $request->hp;
        $item->addr  = $request->addr;
        $item->study = $request->study;
        $item->save();

        return redirect()->route('dashboard.master.teach.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teach $teach)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teach $teach)
    {
        $items  = $teach;
        $action = "Edit Guru";
        return view('teach.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teach $teach)
    {
        $request->validate([
            'name'  => 'required',
            'birth' => 'required',
            'hp'    => 'required',
            'addr'  => 'required',
            'study' => 'required',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
        ]);

        $item        = $teach;
        $item->name  = $request->name;
        $item->birth = $request->birth;
        $item->hp    = $request->hp;
        $item->addr  = $request->addr;
        $item->study = $request->study;
        $item->save();

        return redirect()->route('dashboard.master.teach.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teach $teach)
    {
        $teach->delete();
        return redirect()->route('dashboard.master.teach.index');
    }
}
