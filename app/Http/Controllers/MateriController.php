<?php
namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Student;
use App\Models\Teach;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Materi::with('users')->get();
        return view('home.materi.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $student = Student::latest()->get();
        $teach   = Teach::latest()->get();
        $action  = "Tambah Materi";
        return view('home.materi.form', compact('action', 'student', 'teach'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user' => 'required',
            'role' => 'required|in:3,2',
            'materi'  => 'required|mimes:pdf|max:20480',
        ], ['required' => 'Feild Wajib diisi',
            'in'           => 'FIeld invalid',
        ]);

        if ($request->hasFile('materi')) {
            $path = $request->file('materi')->store('materi', 'public');
        }

        foreach ($request->user as $val) {
            $item       = new Materi;
            $item->pile = $path;
            $item->name = $request->name;
            $item->user = $val;
            $item->save();
        }

        return redirect()->route('dashboard.materi.index');
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
