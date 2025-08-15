<?php
namespace App\Http\Controllers;

use App\Models\Teach;
use App\Models\User;
use Illuminate\Http\Request;

class TeachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Teach::all();
        return view('teach.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Guru";
        return view('teach.form', compact('action'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email Sudah ada.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/teach', 'public');
        }

        $user           = new User;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->nomor    = $request->hp;
        $user->status   = 3;
        $user->password = bcrypt('rahasia');
        $user->save();

        $item        = new Teach;
        $item->img   = $path;
        $item->name  = $request->name;
        $item->birth = $request->birth;
        $item->hp    = $request->hp;
        $item->addr  = $request->addr;
        $item->study = $request->study;
        $item->user  = $user->id;
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
        return view('teach.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teach $teach)
    {
        $request->validate([
            'name'  => 'required',
            'birth' => 'required',
            'hp'    => 'required',
            'addr'  => 'required',
            'study' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required'  => 'Nama sekarang wajib diisi.',
            'birth.required' => 'Tanggal lahir wajib diisi.',
            'addr.required'  => 'Alamat wajib diisi.',
            'hp.required'    => 'Nomor HP wajib diisi.',
            'study.required' => 'Pendidikan Terakhir wajib diisi.',
        ]);

        $path = null;

        $item = $teach;
        if ($request->hasFile('image')) {
            $path      = $request->file('image')->store('teach', 'public');
            $item->img = $path;
        }
        $item->name  = $request->name;
        $item->birth = $request->birth;
        $item->hp    = $request->hp;
        $item->addr  = $request->addr;
        $item->study = $request->study;
        $item->save();

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
