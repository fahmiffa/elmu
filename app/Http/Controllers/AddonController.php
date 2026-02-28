<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Kelas;
use App\Models\Price;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lay = Addon::with('price')->latest()->get();
        return view('master.layanan.index', compact('lay'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Layanan";
        return view('master.layanan.form', compact('action'));
    }

    public function kit()
    {
        $kelas  = Kelas::with('program:id,name', 'units:id,name')->get();
        $action = "Tambah Stater Kit";
        return view('master.kit.index', compact('action', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string',
            'des'     => 'nullable|string',
            'price'   => 'required',
        ], [
            'required' => "Field Wajib disi",
            'unique'   => "Data Program sudah ada",
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item          = new Addon;
        $item->name    = $request->name;
        $item->des     = $request->des;
        $item->save();

        $price          = new Price;
        $price->product = $item->id;
        $price->kelas   = null;
        $price->harga   = $request->price;
        $price->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Layanan berhasil disimpan!']);
        }

        return redirect()->route('dashboard.master.layanan.index')->with('status', 'Layanan berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Addon $addon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Addon $layanan)
    {
        $items  = $layanan->load('price');
        $action = "Edit Layanan";

        return view('master.layanan.form', compact('action', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Addon $layanan)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string',
            'des'     => 'nullable|string',
            'price'   => 'required|numeric',
        ], [
            'required' => "Field Wajib disi",
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $item       = $layanan;
        $item->name = $request->name;
        $item->des  = $request->des;
        $item->save();

        $price        = $layanan->price;
        $price->harga = $request->price;
        $price->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Layanan berhasil diperbarui!']);
        }

        return redirect()->route('dashboard.master.layanan.index')->with('status', 'Layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Addon $layanan)
    {
        $layanan->price->delete();
        $layanan->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Layanan berhasil dihapus!']);
        }

        return redirect()->route('dashboard.master.layanan.index')->with('status', 'Layanan berhasil dihapus!');
    }
}
