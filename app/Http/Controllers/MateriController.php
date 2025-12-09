<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Program;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Materi::with('program')->get();
        return view('master.materi.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $program = Program::latest()->get();
        $action  = "Tambah Materi";
        return view('master.materi.form', compact('action', 'program'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'program' => 'required',
            'materi'  => 'required|mimes:pdf|max:20480',
        ], ['required' => 'Feild Wajib diisi',
        ]);

        if ($request->hasFile('materi')) {
            $path = $request->file('materi')->store('materi', 'public');
        }

        $item             = new Materi;
        $item->pile       = $path;
        $item->program_id = $request->program;
        $item->save();

        return redirect()->route('dashboard.master.materi.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Materi $materi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materi $materi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materi $materi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materi $materi)
    {
        $materi->delete();
        return back();
    }
}
