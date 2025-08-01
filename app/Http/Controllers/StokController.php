<?php
namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Items;
use Auth;
use Illuminate\Http\Request;
use DB;

class StokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('stok.index');
    }

    public function stokJson()
    {
        $app  = Stok::with('items')->get();
        return response()->json($app, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'stok' => 'required',
        ], [
            'item.required' => 'Nama wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
        ]);

        $stok         = new Stok;
        $stok->item   = $request->item;
        $stok->count  = $request->stok;
        $stok->app    = Auth::user()->app;
        $stok->user   = Auth::user()->id;
        $stok->status = 1;
        $stok->save();

        return response()->json(['message' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stok $stok)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stok $stok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stok $stok)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stok $stok)
    {
        //
    }
}
