<?php
namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Price;
use App\Models\Program;
use DB;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Program::with(['kelas', 'price'])->latest()->get();
        return view('master.program.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Program";
        $kelas  = Kelas::all();
        return view('master.program.form', compact('action', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'kit'    => 'required',
            'kode'    => 'required',
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

        $item          = new Program;
        $item->name    = $request->name;
        $item->kode    = $request->kode;
        $item->des     = $request->des;
        $item->level   = $request->level;
        $item->kit     = $request->nominal;
        $item->kit_des = $request->kit_des;
        $item->save();

        for ($i = 0; $i < count($kelas); $i++) {
            $price          = new Price;
            $price->product = $item->id;
            $price->kelas   = $kelas[$i];
            $price->harga   = str_replace(".", null, $harga[$i]);
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
        $data  = $items->price
            ->filter(fn($da) => $da->class !== null)
            ->map(function ($da) {
                return [
                    'id'    => $da->class->id,
                    'price' => $da->id,
                    'name'  => $da->class->name,
                    'value' => number_format($da->harga, 0, '.', '.'),
                ];
            });

        $action = "Edit Program";
        $kelas  = Kelas::all();
        return view('master.program.form', compact('items', 'action', 'kelas', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'name'  => 'required',
            'kode'  => 'required',
            'des'   => 'required',
            'level' => 'required',
            'price' => 'required',

        ], [
            'required' => 'Field wajib diisi.',
        ]);
        DB::beginTransaction();
        try {
            $kelas   = $request->id;
            $idPrice = $request->price;
            $harga   = $request->harga;

            $item          = $program;
            $item->name    = $request->name;
            $item->kode    = $request->kode;
            $item->des     = $request->des;
            $item->level   = $request->level;
            $item->kit     = $request->nominal;
            $item->kit_des = $request->kit_des;
            $item->save();

            $existingIds = Price::where('product', $item->id)->pluck('id')->toArray();
            $incomingIds = array_map('intval', $idPrice);
            $toDelete    = array_diff($existingIds, $incomingIds);
            if (! empty($toDelete)) {
                Price::whereIn('id', $toDelete)->delete();
            }

            for ($i = 0; $i < count($kelas); $i++) {
                $par = Price::where('id', $idPrice[$i])->first();
                if ($par) {
                    $par->harga = str_replace(".", null, $harga[$i]);
                    $par->save();
                } else {
                    $price          = new Price;
                    $price->product = $item->id;
                    $price->kelas   = $kelas[$i];
                    $price->harga   = str_replace(".", null, $harga[$i]);
                    $price->save();
                }
            }

            DB::commit();
            return redirect()->route('dashboard.master.program.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return back()->with('err', 'Terjadi Kesalahan Proses Input');
        }
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
