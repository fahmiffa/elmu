<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Raport;
use App\Models\Student;
use App\Models\Head;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Tymon\JWTAuth\Facades\JWTAuth;

class RaportController extends Controller
{
    public function index()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;
        
        if ($role == 2) {
            $item = Raport::with('murid')->where('student_id', $id)->latest()->get()
                ->map(function ($q) {
                    $data = $q->toArray();
                    $data['url'] = asset('storage/' . $q->file);
                    $data['murid'] = $q->murid->name ?? 'Unknown';
                    return $data;
                });
        } else {
            $muridIds = JWTAuth::user()->data->murid->pluck("students")->toArray();
            $studentUsers = Student::whereIn("id", $muridIds)->pluck("user")->toArray();
            
            $item  = Raport::whereIn('student_id', $studentUsers)
                ->with('murid')
                ->latest()
                ->get()
                ->map(function ($q) {
                    $data = $q->toArray();
                    $data['url'] = asset('storage/' . $q->file);
                    $data['murid'] = $q->murid->name ?? 'Unknown';
                    return $data;
                });
        }
        
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required',
            'student_id'          => 'required',
            // 'teacher'             => 'required',
            // 'leader'              => 'required',
            'score_concept'       => 'required|integer',
            'note_concept'        => 'required',
            'score_concentration' => 'required|integer',
            'note_concentration'  => 'required',
            'score_accuracy'      => 'required|integer',
            'note_accuracy'       => 'required',
            'score_independence'  => 'required|integer',
            'note_independence'   => 'required',
            'strength'            => 'required',
            'progress_period'     => 'required',
            'improvement'         => 'required',
            'category'            => 'required',
            'recommendation'      => 'required',
            'recommendation_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        $data['teacher'] = JWTAuth::user()->data->name;
        $data['leader'] = "Leader";

        $raport = Raport::create($data);
        $this->generatePdf($raport);

        return response()->json([
            'status' => true, 
            'message' => 'Raport berhasil disimpan.',
            'data' => $raport
        ], 200);
    }

    public function update(Request $request, Raport $raport)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required',
            'student_id'          => 'required',
            'score_concept'       => 'required|integer',
            'note_concept'        => 'required',
            'score_concentration' => 'required|integer',
            'note_concentration'  => 'required',
            'score_accuracy'      => 'required|integer',
            'note_accuracy'       => 'required',
            'score_independence'  => 'required|integer',
            'note_independence'   => 'required',
            'strength'            => 'required',
            'progress_period'     => 'required',
            'improvement'         => 'required',
            'category'            => 'required',
            'recommendation'      => 'required',
            'recommendation_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $data = $request->all();
        $raport->update($data);
        $this->generatePdf($raport);

        return response()->json([
            'status' => true, 
            'message' => 'Raport berhasil diperbarui.',
            'data' => $raport
        ], 200);
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
