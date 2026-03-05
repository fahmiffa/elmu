<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Head;
use App\Models\Level;
use App\Models\Student;
use App\Models\User;
use App\Rules\NumberWa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentController extends Controller
{
    public function data()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        if ($role == 2) {
            $student = User::select('id', 'name', 'email', 'status', 'role')
                ->with('data')
                ->where('id', $id)->first();

            $induk          = optional($student->data->reg->first())->induk;
            $student->induk = $induk ? substr($induk, 0, -4) : null;
            $student->data->makeHidden('reg');

            return response()->json($student);
        }

        if ($role == 3) {
            $student = User::select('id', 'name', 'email', 'status', 'role')->where('id', $id)->first();
            return response()->json($student);
        }
    }

    public function Updata(Request $request, $par)
    {
        $id    = JWTAuth::user()->id;
        $siswa = Student::where('user', $id)->first();

        if ($par == "child") {
            $validator = Validator::make($request->all(), [
                'alamat'                => 'required',
                'nama_panggilan'        => 'required',
                'agama'                 => 'required',
                'tempat_tanggal_lahir'  => 'required',
                'tanggal_lahir'         => 'required',
                'cita_cita'             => 'required',
                'hp'                    => 'required',
                'jenis_kelamin'         => 'required',
                'peringkat'             => 'required',
                'prestasi'              => 'required',
                'pendidikan_non_formal' => 'required',
                'sekolah_kelas'         => 'required',
                'alamat_sekolah'        => 'required',
                'sosmed'                => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 400);
            }

            $user        = $siswa->users;
            $user->email = $request->email;
            $user->save();

            $siswa->alamat                = $request->alamat;
            $siswa->nama_panggilan        = $request->nama_panggilan;
            $siswa->place                 = $request->tempat_tanggal_lahir;
            $siswa->birth                 = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir);
            $siswa->dream                 = $request->cita_cita;
            $siswa->hp_siswa              = $request->hp;
            $siswa->agama                 = $request->agama;
            $siswa->gender                = $request->jenis_kelamin == "Laki-laki" ? 1 : 2;
            $siswa->study                 = $request->sekolah_kelas;
            $siswa->rank                  = $request->peringkat;
            $siswa->pendidikan_non_formal = $request->pendidikan_non_formal;
            $siswa->prestasi              = $request->prestasi;
            $siswa->alamat_sekolah        = $request->alamat_sekolah;
            $siswa->sosmedChild           = $request->sosmed;
            $siswa->save();
        }

        if ($par == "parent") {
            $validator = Validator::make($request->all(), [
                'nama_ayah'      => 'required',
                'pekerjaan_ayah' => 'required',
                'nama_ibu'       => 'required',
                'pekerjaan_ibu'  => 'required',
                'sosmed'         => 'required',
                'hp'             => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 400);
            }

            $siswa->dad         = $request->nama_ayah;
            $siswa->dadJob      = $request->pekerjaan_ayah;
            $siswa->mom         = $request->nama_ibu;
            $siswa->momJob      = $request->pekerjaan_ibu;
            $siswa->sosmedOther = $request->sosmed;
            $siswa->hp_parent   = $request->hp;
            $siswa->save();
        }

        return response()->json(['status' => true], 200);
    }

    public function murid()
    {
        $id = JWTAuth::user()->id;
        $da = \App\Models\Teach::where('user', $id)->first();
        if ($da) {
            $items    = Head::where('unit', $da->unit_id)->where('done', 0)->with('murid:id,name')->get();
            $allMurid = collect();

            foreach ($items as $head) {
                $allMurid->push($head->murid);
            }

            return response()->json([
                'murid' => $allMurid->values()->unique('id')->all(),
            ]);
        } else {
            return response()->json([]);
        }
    }

    public function reg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grade_id'              => 'required',
            'kelas'                 => 'required',
            'gender'                => 'nullable|in:1,2',
            'place'                 => 'nullable|string',
            'birth'                 => 'nullable|date',
            'dad'                   => 'nullable|string',
            'dadJob'                => 'nullable|string',
            'mom'                   => 'nullable|string',
            'momJob'                => 'nullable|string',
            'hp_parent'             => 'nullable|string',
            'kontrak'               => 'required',
            'program'               => 'required',
            'unit'                  => 'required',
            'hp'                    => ['required', 'unique:users,nomor', new NumberWa()],
            'email'                 => 'required|email|unique:users,email',
            'name'                  => 'required|string',
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
            'panggilan'             => 'nullable|string',
        ], [
            'hp.required'    => 'Nomor wajib diisi.',
            'hp.unique'      => 'Nomor sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $programCheck = \App\Models\Program::find($request->program);
        if ($programCheck && ($programCheck->extend == 1 || $programCheck->extend == true)) {
            return response()->json([
                'errors' => [
                    'program' => ['Program ini hanya tersedia untuk upgrade, tidak untuk pendaftaran baru.']
                ]
            ], 400);
        }

        DB::beginTransaction();
        try {
            $path = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images', 'public');
            }

            $user           = new User;
            $user->name     = UserName($request->name);
            $user->email    = $request->email;
            $user->nomor    = $request->hp;
            $user->role     = 2;
            $user->status   = 1;
            $user->password = bcrypt('murik@');
            $user->save();

            $siswa                        = new Student;
            $siswa->user                  = $user->id;
            $siswa->name                  = $request->name;
            $siswa->nama_panggilan        = $request->panggilan;
            $siswa->img                   = $path;
            $siswa->grade_id              = $request->grade_id;
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
            $siswa->hp_parent             = $request->hp;
            $siswa->study                 = $request->study;
            $siswa->rank                  = $request->rank;
            $siswa->pendidikan_non_formal = $request->pendidikan_non_formal;
            $siswa->prestasi              = $request->prestasi;
            $siswa->gender                = $request->gender;
            $siswa->save();

            $unit = Head::where('unit', $request->unit)->count() + 1;

            $price = \App\Models\Price::where('kelas', $request->kelas)
                ->where('product', $request->program)
                ->first();

            $head           = new Head;
            $head->number   = $unit;
            $head->number   = Head::where('unit', $request->unit)->count() + 1;
            $head->global   = Head::count() + 1;
            $head->students = $siswa->id;
            $head->unit     = $request->unit;
            $head->kelas    = $request->kelas;
            $head->price    = $price->id;
            $head->program  = $request->program;
            $head->payment  = $request->kontrak;
            $head->save();

            $paid        = new \App\Models\Paid;
            $paid->head  = $head->id;
            $paid->bulan = date("m");
            $paid->tahun = date("Y");
            $paid->first = 1;
            $paid->save();

            $level             = new Level;
            $level->student_id = $siswa->id;
            $level->head       = $head->id;
            $level->status     = 1;
            $level->save();

            $to       = '62' . substr($user->nomor, 1);
            $response = Http::post(env('URL_WA') . '/send', [
                'number'  => env('NUMBER_WA'),
                'to'      => $to,
                'message' => "Selamat Anda Berhasil mendaftar\nPassword akun anda : *murik@*",
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return response()->json(['error' => $e], 500);
        }
    }
}
