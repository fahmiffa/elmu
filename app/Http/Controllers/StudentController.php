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
                $user           = new User;
                $user->name     = UserName($row[1]);
                $user->role     = 2;
                $user->status   = 1;
                $user->password = bcrypt('murik@');
                $user->save();

                $siswa           = new Student;
                $siswa->user     = $user->id;
                $siswa->name     = $row[1];
                $siswa->grade_id = $request->grade;
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
                $head->price    = $price->id;
                $head->old      = 1;
                $head->program  = $request->program;
                $head->payment  = $request->kontrak;
                $head->save();

                $level             = new Level;
                $level->student_id = $siswa->id;
                $level->head       = $head->id;
                $level->status     = 1;
                $level->save();
            }

            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return back()->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
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
