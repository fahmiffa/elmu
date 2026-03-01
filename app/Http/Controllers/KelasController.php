<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Kelas::all();
        return view('master.kelas.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah kelas";
        return view('master.kelas.form', compact('action'));
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

        $item       = new Kelas;
        $item->name = $request->name;
        $item->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kelas berhasil disimpan!',
                'data' => $item
            ]);
        }

        return redirect()->route('dashboard.master.kelas.index')->with('status', 'Kelas berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        $items  = $kela;
        $action = "Edit kelas";
        return view('master.kelas.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Nama sekarang wajib diisi.',
        ]);

        $item       = $kela;
        $item->name = $request->name;
        $item->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kelas berhasil diperbarui!',
                'data' => $item
            ]);
        }

        return redirect()->route('dashboard.master.kelas.index')->with('status', 'Kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        // Cek apakah kelas sudah digunakan di data murid (Head)
        $used = Head::where('kelas', $kela->id)->exists();

        if ($used) {
            $message = 'Kelas tidak dapat dihapus karena sudah digunakan oleh murid.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 422);
            }
            return back()->with('err', $message);
        }

        try {
            $kela->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Kelas berhasil dihapus!']);
            }

            return redirect()->route('dashboard.master.kelas.index')->with('status', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus kelas: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
}
