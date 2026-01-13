<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Payment;
use App\Models\Price;
use App\Models\Student;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items   = Student::with('users')->get();
        $kelas   = Kelas::with('program:id,name', 'units:id,name')->get();
        $kontrak = Payment::all();
        $grade   = Grade::all();
        return view('master.students.index', compact('items', 'kelas', 'grade', 'kontrak'));
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
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = Excel::toArray([], $request->file('file'));
            $val  = $data[0];
            array_shift($val);

            foreach ($val as $row) {
                if (! empty($row[1]) || ! empty($row[2])) {

                    if (! empty($row[15]) && ! preg_match('/^08\d+$/', $row[15])) {
                        throw new Exception('Format nomor hp tidak valid');
                    }

                    $birth = null;
                    if (! empty($row[4])) {
                        $d = \DateTime::createFromFormat('d/m/Y', $row[4]);
                        if ($d && $d->format('d/m/Y') === $row[4]) {
                            $birth = $d->format('Y-m-d');
                        }
                    }

                    $user           = new User;
                    $user->name     = ! empty($row[2]) ? UserName($row[2]) : null;
                    $user->role     = 2;
                    $user->status   = 1;
                    $user->nomor    = ! empty($row[15]) ? $row[15] : null;
                    $user->password = bcrypt('murik@');
                    $user->save();

                    $siswa                        = new Student;
                    $siswa->user                  = $user->id;
                    $siswa->place                 = $row[3] ?? null;
                    $siswa->birth                 = $birth;
                    $siswa->agama                 = $row[5] ?? null;
                    $siswa->sekolah_kelas         = $row[6] ?? null;
                    $siswa->alamat                = $row[8] ?? null;
                    $siswa->mom                   = $row[10] ?? null;
                    $siswa->momJob                = $row[13] ?? null;
                    $siswa->dad                   = $row[11] ?? null;
                    $siswa->dadJob                = $row[12] ?? null;
                    $siswa->sosmedChild           = $row[14] ?? null;
                    $siswa->name                  = $row[2] ?? null;
                    $siswa->hp_parent             = $row[15] ?? null;
                    $siswa->gender                = $row[17] ?? null;
                    $siswa->alamat_sekolah        = $row[18] ?? null;
                    $siswa->sosmedOther           = $row[19] ?? null;
                    $siswa->rank                  = $row[20] ?? null;
                    $siswa->pendidikan_non_formal = $row[21] ?? null;
                    $siswa->prestasi              = $row[22] ?? null;
                    $siswa->grade_id              = $request->grade;
                    $siswa->save();

                    $price = Price::where('kelas', $request->kelas)
                        ->where('product', $request->program)
                        ->first();

                    $head           = new Head;
                    $head->number   = Head::where('unit', $request->unit)->count() + 1;
                    $head->global   = Head::count() + 1;
                    $head->students = $siswa->id;
                    $head->unit     = $request->unit;
                    $head->kelas    = $request->kelas;
                    $head->price    = $price->id ?? null;
                    $head->old      = 1;
                    $head->program  = $request->program;
                    $head->payment  = $request->kontrak;
                    $head->save();

                    $level             = new Level;
                    $level->student_id = $siswa->id;
                    $level->head       = $head->id;
                    $level->level      = $row[16] ?? null; // perbaikan
                    $level->status     = 1;
                    $level->save();

                }
            }

            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['import' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $action = "Edit Murid " . $student->name;
        $grade  = Grade::all();
        return view('master.students.form', compact('student', 'action', 'grade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'grade'                 => 'required',
            'gender'                => 'nullable|in:1,2',
            'place'                 => 'nullable|string',
            'birth'                 => 'nullable|date',
            'dad'                   => 'nullable|string',
            'dadJob'                => 'nullable|string',
            'mom'                   => 'nullable|string',
            'momJob'                => 'nullable|string',
            'hp_parent'             => 'nullable|string',
            'email'                 => 'required',

            'name'                  => 'nullable|string',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sekolah_kelas'         => 'nullable|string',
            'alamat'                => 'nullable|string',
            'alamat_sekolah'        => 'nullable|string',
            'dream'                 => 'nullable|string',
            'hp_siswa'              => 'nullable|string',
            'agama'                 => 'nullable|string',
            'sosmedChild'           => 'nullable|string',
            'sosmedOther'           => 'nullable|string',
            'study'                 => 'nullable|string',
            'rank'                  => 'nullable|string',
            'pendidikan_non_formal' => 'nullable|string',
            'prestasi'              => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $path = null;

            $user        = $student->users;
            $user->name  = $request->name;
            $user->email = $request->email;
            $user->save();

            $siswa       = $student;
            $siswa->user = $user->id;
            $siswa->name = $request->name;
            if ($request->hasFile('image')) {
                $path       = $request->file('image')->store('images', 'public');
                $siswa->img = $path;
            }
            $siswa->jenjang               = $request->grade;
            $siswa->alamat                = $request->alamat;
            $siswa->place                 = $request->place;
            $siswa->birth                 = $request->birth;
            $siswa->sekolah_kelas         = $request->sekolah_kelas;
            $siswa->alamat_sekolah        = $request->alamat_sekolah;
            $siswa->dream                 = $request->dream;
            $siswa->hp_siswa              = $request->hp_siswa;
            $siswa->agama                 = $request->agama;
            $siswa->sosmedChild           = $request->sosmedChild;
            $siswa->sosmedOther           = $request->sosmedOther;
            $siswa->dad                   = $request->dad;
            $siswa->dadJob                = $request->dadJob;
            $siswa->mom                   = $request->mom;
            $siswa->momJob                = $request->momJob;
            $siswa->hp_parent             = $request->hp_parent;
            $siswa->study                 = $request->study;
            $siswa->rank                  = $request->rank;
            $siswa->pendidikan_non_formal = $request->pendidikan_non_formal;
            $siswa->prestasi              = $request->prestasi;
            $siswa->gender                = $request->gender;
            $siswa->save();

            DB::commit();

            return redirect()->route('dashboard.master.student.index');

        } catch (\Throwable $e) {
            DB::rollback();

            // Hapus gambar jika sempat ter-upload
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
