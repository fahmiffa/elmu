<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Price;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Program::with(['kelas','price'])->latest()->get();
        return view('program.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Program";
        $kelas  = Kelas::all();
        return view('program.form', compact('action', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'des'     => 'required',
            'level'   => 'required',
            'harga'   => 'required',
            'harga.*' => 'required',
            'kelas'   => 'required',
            'kelas.*' => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);
        $kelas = $request->id;
        $harga = $request->harga;

        $item        = new Program;
        $item->name  = $request->name;
        $item->des   = $request->des;
        $item->level = $request->level;
        $item->save();

        for ($i = 0; $i < count($kelas); $i++) {
            $price          = new Price;
            $price->product = $item->id;
            $price->kelas   = $kelas[$i];
            $price->harga   = $harga[$i];
            $price->save();
        }

        return redirect()->route('dashboard.master.program.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $items = $program->load('price.class');
        $data = $items->price->map(function ($kp) {
            return [
                'id'    => $kp->class->id,
                'price' => $kp->id,
                'name'  => $kp->class->name,
                'value' => $kp->harga,
            ];
        });

        $action = "Edit Program";
        $kelas  = Kelas::all();
        return view('program.form', compact('items', 'action', 'kelas', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name'  => 'required',
            'des'   => 'required',
            'level' => 'required',
            'price' => 'required',

        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $kelas = $request->id;
        $price = $request->price;
        $harga = $request->harga;

        $item        = $program;
        $item->name  = $request->name;
        $item->des   = $request->des;
        $item->level = $request->level;
        $item->save();

        $pr = Price::where('product', $item->id)->pluck('id')->toArray();
        $remove = array_values(array_diff($pr,$price));

        for ($i=0; $i < count($remove); $i++) { 
            Price::where('id',$remove[$i])->delete();
        }

        for ($i = 0; $i < count($price); $i++) {
            Price::where('id', $price[$i])->update([
                'product'=>$item->id,
                'harga' => $harga[$i],
                'kelas' => $kelas[$i]
            ]);
        }

        return redirect()->route('dashboard.master.program.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('dashboard.master.program.index');
    }
}
