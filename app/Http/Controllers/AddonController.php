<?php
namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Addon::with('price')->latest()->get();
        return view('layanan.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Layanan";
        return view('layanan.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'des'   => 'nullable|string',
            'price' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/layanan', 'public');
        }

        $item       = new Addon;
        $item->name = $request->name;
        $item->img  = $path;
        $item->des  = $request->des;
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
        $items  = $layanan;
        $action = "Edit Layanan";
        return view('layanan.form', compact('action', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Addon $layanan)
    {
        $request->validate([
            'name'  => 'required|string',
            'des'   => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/layanan', 'public');
        }

        $item       = $layanan;
        $item->name = $request->name;
        $item->img  = $path;
        $item->des  = $request->des;
        $item->save();

        Price::where('product',$item->id)->update(['harga'=>$request->price]);

        return redirect()->route('dashboard.master.layanan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Addon $layanan)
    {
        $layanan->delete();
        return back();
    }
}
