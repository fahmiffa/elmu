<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
        $validator = Validator::make($request->all(), [
            'program' => 'required',
            'materi'  => 'required|mimes:pdf|max:20480',
        ], [
            'required' => 'Feild Wajib diisi',
            'mimes' => 'File harus berupa PDF',
            'max' => 'Ukuran file maksimal 20MB',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $path = null;
        if ($request->hasFile('materi')) {
            $path = $request->file('materi')->store('materi', 'public');
        }

        $item             = new Materi;
        $item->pile       = $path;
        $item->program_id = $request->program;
        $item->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Materi berhasil disimpan!']);
        }

        return redirect()->route('dashboard.master.materi.index')->with('status', 'Materi berhasil disimpan!');
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
        if ($materi->pile && Storage::disk('public')->exists($materi->pile)) {
            Storage::disk('public')->delete($materi->pile);
        }
        $materi->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Materi berhasil dihapus!']);
        }

        return redirect()->route('dashboard.master.materi.index')->with('status', 'Materi berhasil dihapus!');
    }
}
