<?php
namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Kelas;
use App\Models\Price;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $request->validate([
            'name'    => 'required|string',
            'des'     => 'nullable|string',
            'price'   => 'required',
        ], [
            'required' => "Field Wajib disi",
            'unique'   => "Data Program sudah ada",
        ]);

        $item          = new Addon;
        $item->name    = $request->name;
        $item->des     = $request->des;
        $item->save();

        $price          = new Price;
        $price->product = $item->id;
        $price->kelas   = null;
        $price->harga   = $request->price;
        $price->save();

        return redirect()->route('dashboard.master.layanan.index');
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
        $request->validate([
            'name'    => 'required|string',
            'des'     => 'nullable|string',
            'price'   => 'required|numeric',
        ]);

        $item       = $layanan;
        $item->name = $request->name;
        $item->des  = $request->des;
        $item->save();

        $price        = $layanan->price;
        $price->harga = $request->price;
        $price->save();
        return redirect()->route('dashboard.master.layanan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Addon $layanan)
    {
        $layanan->delete();
        $layanan->price->delete();
        return back();
    }
}
