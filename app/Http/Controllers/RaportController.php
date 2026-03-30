<?php
namespace App\Http\Controllers;

use App\Models\Raport;
use App\Models\Student;
use App\Models\Unit;
use App\Models\Head;
use App\Models\Program;
use App\Models\Kelas;
use App\Models\Teach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $items = Raport::query();

        if ($user->role == 4) {
            $items->whereHas('murid.reg.units.zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
        }

        $items = $items->get();
        return view('home.raport.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user     = Auth::user();
        $student  = Student::with(['reg.programs', 'reg.units', 'reg.class', 'grade'])->latest();
        $units    = Unit::query();
        $teachers = Teach::query();

        if ($user->role == 4) {
            $units->whereHas('zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
            $student->whereHas('reg.units.zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
            $teachers->whereHas('unit.zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
        }

        $student  = $student->get();
        $units    = $units->get();
        $programs = Program::all();
        $kelas    = Kelas::all();
        $teachers = $teachers->get();

        $action   = "Tambah Raport";
        return view('home.raport.form', compact('action', 'student', 'units', 'programs', 'kelas', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required',
            'student_id'          => 'required',
            'teacher'             => 'nullable',
            'leader'              => 'nullable',
            'score_concept'       => 'nullable|integer',
            'note_concept'        => 'nullable',
            'score_concentration' => 'nullable|integer',
            'note_concentration'  => 'nullable',
            'score_accuracy'      => 'nullable|integer',
            'note_accuracy'       => 'nullable',
            'score_independence'  => 'nullable|integer',
            'note_independence'   => 'nullable',
            'strength'            => 'nullable',
            'progress_period'     => 'nullable',
            'improvement'         => 'nullable',
            'category'            => 'nullable',
            'recommendation'      => 'nullable',
            'recommendation_note' => 'nullable',
        ]);

        // Find Head ID
        if ($request->student_id && $request->program) {
            $student = Student::where('user', $request->student_id)->first();
            if ($student) {
                $head = Head::where('students', $student->id)
                    ->whereHas('programs', function($q) use ($request) {
                        $q->where('name', $request->program);
                    })->first();
                $data['head_id'] = $head?->id;
            }
        }

        $raport = Raport::create($data);
        $this->generatePdf($raport);

        return redirect()->route('dashboard.raport.index');
    }

    public function edit(Raport $raport)
    {
        $user     = Auth::user();
        $student  = Student::with(['reg.programs', 'reg.units', 'reg.class', 'grade'])->latest();
        $units    = Unit::query();
        $teachers = Teach::query();

        if ($user->role == 4) {
            $units->whereHas('zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
            $student->whereHas('reg.units.zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
            $teachers->whereHas('unit.zone', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
        }

        $student  = $student->get();
        $units    = $units->get();
        $programs = Program::all();
        $kelas    = Kelas::all();
        $teachers = $teachers->get();

        $items    = $raport;
        $action   = "Edit Raport";
        return view('home.raport.form', compact('action', 'student', 'items', 'units', 'programs', 'kelas', 'teachers'));
    }


    public function update(Request $request, Raport $raport)
    {
        $data = $request->validate([
            'name'                => 'required',
            'student_id'          => 'required',
            'teacher'             => 'nullable',
            'leader'              => 'nullable',
            'score_concept'       => 'nullable|integer',
            'note_concept'        => 'nullable',
            'score_concentration' => 'nullable|integer',
            'note_concentration'  => 'nullable',
            'score_accuracy'      => 'nullable|integer',
            'note_accuracy'       => 'nullable',
            'score_independence'  => 'nullable|integer',
            'note_independence'   => 'nullable',
            'strength'            => 'nullable',
            'progress_period'     => 'nullable',
            'improvement'         => 'nullable',
            'category'            => 'nullable',
            'recommendation'      => 'nullable',
            'recommendation_note' => 'nullable',
        ]);

        // Find Head ID
        if ($request->student_id && $request->program) {
            $student = Student::where('user', $request->student_id)->first();
            if ($student) {
                $head = Head::where('students', $student->id)
                    ->whereHas('programs', function($q) use ($request) {
                        $q->where('name', $request->program);
                    })->first();
                $data['head_id'] = $head?->id;
            }
        }

        $raport->update($data);
        $this->generatePdf($raport);

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

    public function preview($id = null)
    {
        $items = $id ? Raport::with(['murid', 'reg.programs', 'reg.units.zone', 'reg.class', 'reg.latestLevel'])->findOrFail($id) : null;
        $murid = $items->murid ?? null;
        
        $pdf = Pdf::loadView('home.raport.raport', compact('items', 'murid'))
                  ->setPaper('a4', 'portrait');
        
        return $pdf->stream('raport-' . ($murid->name ?? 'siswa') . '.pdf');
    }

    private function generatePdf(Raport $raport)
    {
        $items = Raport::with(['murid', 'reg.programs', 'reg.units.zone', 'reg.class', 'reg.latestLevel'])->findOrFail($raport->id);
        $murid = $items->murid ?? null;

        $pdf = Pdf::loadView('home.raport.raport', compact('items', 'murid'))
                  ->setPaper('a4', 'portrait');
        
        $fileName = 'raport/raport-' . ($murid->user ?? $raport->id) . '-' . time() . '.pdf';
        
        if ($raport->file && Storage::disk('public')->exists($raport->file)) {
            Storage::disk('public')->delete($raport->file);
        }

        Storage::disk('public')->put($fileName, $pdf->output());
        $raport->update(['file' => $fileName]);
    }
}
