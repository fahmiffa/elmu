<?php
namespace App\Http\Controllers;

use App\Models\Raport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RaportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Raport::all();
        return view('home.raport.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $student = Student::latest()->get();
        $action  = "Tambah Raport";
        return view('home.raport.form', compact('action', 'student'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'pdf'   => 'required|mimes:pdf|max:20480',
            'murid' => 'required',
        ], ['required' => 'Feild Wajib diisi',
            'in'           => 'FIeld invalid',
        ]);

        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('raport', 'public');
        }

        $item             = new Raport;
        $item->file       = $path;
        $item->name       = $request->name;
        $item->student_id = $request->murid;
        $item->save();

        return redirect()->route('dashboard.raport.index');
    }
    public function destroy(Raport $raport)
    {
        if (isset($raport->file)) {
            Storage::disk('public')->delete($raport->file);
        }
        $raport->delete();
        return back();
    }
}
