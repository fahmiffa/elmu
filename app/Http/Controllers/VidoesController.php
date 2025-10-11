<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Vidoes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class VidoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Vidoes::all();
        return view('home.video.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $student = Student::latest()->get();
        $action  = "Tambah Video";
        return view('home.video.form', compact('action', 'student'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'role'  => 'required|in:3,2',
            'video' => 'required|mimes:mp4|max:20480',
            'murid' => 'required_if:role,murid|exists:students,user',
        ], ['required' => 'Feild Wajib diisi',
            'in'           => 'FIeld invalid',
        ]);

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('videos', 'public');
        }

        $item       = new Vidoes;
        $item->pile = $path;
        $item->name = $request->name;
        $item->to   = $request->role;
        if ($request->to == 3) {
            $item->user = $request->murid;
        }
        $item->save();

        return redirect()->route('dashboard.video.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Vidoes $vidoes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vidoes $vidoes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vidoes $vidoes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vidoes $video)
    {
        if (isset($video->pile)) {
            Storage::disk('public')->delete($video->pile);
        }
        $video->delete();
        return back();
    }
}
