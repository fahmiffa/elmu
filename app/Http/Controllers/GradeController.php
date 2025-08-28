<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $items = Grade::all();
        return view('master.grade.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Jenjang";
        return view('master.grade.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
        ]);

        $item       = new Grade;
        $item->name = $request->name;
        $item->save();

        return redirect()->route('dashboard.master.grade.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        $items  = $grade;
        $action = "Edit Jenjang";
        return view('master.grade.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
        ]);

        $item       = $grade;
        $item->name = $request->name;
        $item->save();

        return redirect()->route('dashboard.master.grade.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('dashboard.master.grade.index');
    }
}
