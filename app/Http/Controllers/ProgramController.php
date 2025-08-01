<?php
namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Program::all();
        return view('program.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Program";
        return view('program.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'des'   => 'required',
            'level' => 'required',
            'price' => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $item        = new Program;
        $item->name  = $request->name;
        $item->des   = $request->des;
        $item->level = $request->level;
        $item->price = $request->price;
        $item->save();

        return redirect()->route('dashboard.master.program.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $items  = $program;
        $action = "Edit Program";
        return view('program.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name'  => 'required',
            'des'   => 'required',
            'level' => 'required',
            'price' => 'required',

        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $item        = $program;
        $item->name  = $request->name;
        $item->des   = $request->des;
        $item->level = $request->level;
        $item->price = $request->price;
        $item->save();

        return redirect()->route('dashboard.master.program.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('dashboard.master.program.index');
    }
}
