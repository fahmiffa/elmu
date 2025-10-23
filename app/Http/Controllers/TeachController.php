<?php
namespace App\Http\Controllers;

use App\Models\Teach;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class TeachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Teach::with('akun','unit')->get();
        return view('master.teach.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Guru";
        $unit   = Unit::all();
        return view('master.teach.form', compact('action', 'unit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'birth' => 'required',
            'email' => 'required|unique:users,email',
            'hp'    => 'required',
            'addr'  => 'required',
            'study' => 'required',
            'unit'  => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email Sudah ada.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
            'required'       => 'Field wajib diisi.',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/teach', 'public');
        }

        $user           = new User;
        $user->name     = UserName($request->name);
        $user->email    = $request->email;
        $user->nomor    = $request->hp;
        $user->status   = 3;
        $user->role     = 3;
        $user->password = bcrypt('rahasia');
        $user->save();

        $item          = new Teach;
        $item->img     = $path;
        $item->unit_id = $request->unit;
        $item->name    = $request->name;
        $item->birth   = $request->birth;
        $item->hp      = $request->hp;
        $item->addr    = $request->addr;
        $item->study   = $request->study;
        $item->user    = $user->id;
        $item->save();

        return redirect()->route('dashboard.master.teach.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Teach $teach)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teach $teach)
    {
        $items  = $teach;
        $action = "Edit Guru";
        $unit   = Unit::all();
        return view('master.teach.form', compact('items', 'action', 'unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teach $teach)
    {
        $teach->load('akun');

        $request->validate([
            'name'  => 'required',
            'birth' => 'required',
            'hp'    => 'required',
            'addr'  => 'required',
            'study' => 'required',
            'unit'  => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
            'unit.required'  => 'Unit wajib dipilih.',
            'image.max'      => 'Ukuran gambar maksimal 2MB.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Gambar harus berformat jpeg, png, atau jpg.',
        ]);

        // Update image if present
        if ($request->hasFile('image')) {
            if ($teach->img && \Storage::disk('public')->exists($teach->img)) {
                \Storage::disk('public')->delete($teach->img);
            }

            $path       = $request->file('image')->store('images/teach', 'public');
            $teach->img = $path;
        }

        // Update other fields
        $teach->name    = $request->name;
        $teach->birth   = $request->birth;
        $teach->hp      = $request->hp;
        $teach->addr    = $request->addr;
        $teach->study   = $request->study;
        $teach->unit_id = $request->unit;

        // Save updated model
        $teach->save();

        return redirect()->route('dashboard.master.teach.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teach $teach)
    {
        $teach->delete();
        return redirect()->route('dashboard.master.teach.index');
    }
}
