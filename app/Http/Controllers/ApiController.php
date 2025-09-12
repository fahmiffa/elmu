<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Paid;
use App\Models\Price;
use App\Models\Program;
use App\Models\Schedule;
use App\Models\Schedules_students;
use App\Models\Student;
use App\Models\Teach;
use App\Models\Unit;
use App\Models\User;
use App\Rules\NumberWa;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{

    public function UpJadwal(Request $request)
    {

    }

    public function jadwal()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        if ($role == 3) {
            $da    = Teach::where('user', $id)->first();
            $items = Schedule::select('id', 'unit', 'kelas', 'program', 'status')
                ->with(
                    'meet:id,name,schedule_id,status',
                    'meet.waktu:id,schedule_meet_id,waktu,status,ket',
                    'murid:id,name',
                    'units:id,name',
                    'programs:id,kode,name',
                    'class:id,name'
                )
                ->where('unit', $da->unit_id)
                ->get()
                ->each(function ($items) {
                    $items->meet->each->makeHidden('schedule_id');
                    $items->meet->each(function ($meet) {
                        $meet->waktu->each(function ($waktu) {
                            $waktu->makeHidden('schedule_meet_id');
                        });
                    });
                    $items->murid->each->makeHidden('pivot');
                    $items->units->makeHidden('kelasn', 'unit');
                    $items->makeHidden('unit', 'id', 'kelas', 'status', 'program');
                });
        } else {
            $da    = Student::where('user', $id)->first();
            $items = Schedules_students::select('id', 'schedule_id')
                ->where('student_id', $da->id)
                ->with(
                    'jadwal.programs:id,name,kode',
                    'jadwal.class:id,name',
                    'jadwal.meet:id,name,schedule_id,status',
                    'jadwal.meet.waktu:id,waktu,ket,schedule_meet_id,status'
                )
                ->get()
                ->map(function ($item) {
                    $item->jadwal->makeHidden('unit', 'kelas', 'program');
                    return $item->jadwal;
                });

        }

        return response()->json($items);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            $newToken = JWTAuth::refresh($token);

            return response()->json([
                'token'      => $newToken,
                'expires_in' => auth('api')->factory()->getTTL() * 1,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token refresh failed'], 401);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (! $user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // if ($user->status == 0) {
        //     return response()->json(['error' => 'Akun tidak aktif'], 403); // 403 Forbidden
        // }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        if ($request->fcm) {
            $user->fcm = $request->fcm;
            $user->save();
        }
        return response()->json([
            'token'      => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 1,
            'role'       => $user->role,
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function kelas()
    {
        $products = Kelas::select('id', 'name')
            ->with('units:id,name', 'program:id,name')
            ->get()
            ->each(function ($items) {
                $items->units->each->makeHidden('pivot');
                $items->program->each->makeHidden('pivot');
            });
        $grades = Grade::select('id', 'name')->get();
        return response()->json(['items' => $products, 'grade' => $grades]);
    }

    public function program()
    {
        $id       = JWTAuth::user()->id;
        $products = Program::select('id', 'name')->get();
        return response()->json(['items' => $products]);
    }

    public function unit()
    {
        $id       = JWTAuth::user()->id;
        $products = Unit::select('id', 'name')->get();
        return response()->json(['items' => $products]);
    }

    public function price($kelas, $product)
    {
        $id       = JWTAuth::user()->id;
        $products = Price::select('id', 'harga', 'product', 'kelas')
            ->where('kelas', $kelas)
            ->where('product', $product)
            ->with(['class:id,name', 'program:id,name'])
            ->get();
        return response()->json(['items' => $products]);
    }

    public function bill()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        $res = Student::select('id', 'name', 'gender')
            ->with(
                'reg:id,students,price,unit,number,program',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                'reg.bill:id,head,time,bulan,tahun,via,status,first',
            );

        if ($role == 2) {
            $student = $res->where('user', $id)->first();
        }

        if ($role == 3) {
            $guru    = Teach::where('user', $id)->first();
            $unit    = $guru->unit_id;
            $student = $res->whereHas('reg.units', function ($q) use ($unit) {
                $q->where('unit', $unit);
            })
            ->get();
        }
        return response()->json($student);
    }

    public function tagihan()
    {
        $id = JWTAuth::user()->id;

        $student = Student::where('user', $id)
            ->with('reg.bill', 'reg.prices')
            ->first();

        $bill          = [];
        $hasStatusZero = false;
        foreach ($student->reg as $val) {
            foreach ($val->bill as $b) {
                if ($b->status == 0) {
                    $hasStatusZero = true;
                    $bill[]        = [
                        'id'     => $b->id,
                        'status' => $b->status,
                        'ket'    => $b->ket,
                        'via'    => $b->via,
                        'time'   => $b->time,
                        'bulan'  => $b->bulan,
                        'total'  => $b->total,
                    ];
                }
            }
        }
        if (! $hasStatusZero) {
            $bill = [];
        }
        return response()->json($bill);
    }

    public function data()
    {
        $id      = JWTAuth::user()->id;
        $Student = User::select('id', 'name', 'email', 'status', 'role')
            ->with('data')
            ->where('id', $id)->first();
        return response()->json($Student);
    }

    public function billStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $paid         = Paid::where('id', $request->bill)->firstOrFail();
        $paid->status = 1;
        $paid->time   = date("Y-m-d H:i:s");
        $paid->ket    = $request->ket;
        $paid->via    = $request->via;
        $paid->save();

        return response()->json([
            'status' => true,
        ]);

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
            // 'hp'                    => 'required|unique:users,nomor',
            'hp'                    => ['required', 'unique:users,nomor', new NumberWa()],
            'email'                 => 'required|email|unique:users,email',

            // Optional
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
        ],
            [
                'hp.required'    => 'Nomor HP wajib diisi.',
                'hp.unique'      => 'Nomor HP sudah terdaftar.',
                'email.required' => 'Email wajib diisi.',
                'email.email'    => 'Email tidak valid.',
                'email.unique'   => 'Email sudah terdaftar.',
            ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }
        DB::beginTransaction();
        try {
            $path = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images', 'public');
            }

            $user           = new User;
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->nomor    = $request->hp;
            $user->role     = 2;
            $user->status   = 0;
            $user->password = bcrypt('murik@');
            $user->save();

            $siswa                        = new Student;
            $siswa->user                  = $user->id;
            $siswa->name                  = $request->name;
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
            $siswa->hp_parent             = $request->hp_parent;
            $siswa->study                 = $request->study;
            $siswa->rank                  = $request->rank;
            $siswa->pendidikan_non_formal = $request->pendidikan_non_formal;
            $siswa->prestasi              = $request->prestasi;
            $siswa->gender                = $request->gender;
            $siswa->save();

            $unit = Head::where('unit', $request->unit)->count() + 1;

            $price = Price::where('kelas', $request->kelas)
                ->where('product', $request->program)
                ->first();

            $head           = new Head;
            $head->number   = $unit;
            $head->students = $siswa->id;
            $head->unit     = $request->unit;
            $head->kelas    = $request->kelas;
            $head->price    = $price->id;
            $head->program  = $request->program;
            $head->payment  = $request->kontrak;
            $head->save();

            $paid        = new Paid;
            $paid->head  = $head->id;
            $paid->bulan = date("m");
            $paid->tahun = date("Y");
            $paid->first = 1;
            $paid->save();

            $to       = '62' . substr($user->nomor, 1);
            $response = Http::post('http://192.168.18.22:3000/api/send', [
                'number'  => env('NumberWa'),
                'to'      => $to,
                'message' => "Selamat Anda Berhasil mendaftar\nPassword akun anda : Murik@",
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
