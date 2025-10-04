<?php
namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\Price;
use App\Models\Program;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentPresent;
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

    public function Updata(Request $request, $par)
    {
        $id    = JWTAuth::user()->id;
        $siswa = Student::where('user', $id)->first();

        if ($par == "child") {
            $validator = Validator::make($request->all(), [
                'alamat'                => 'required',
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

            $siswa->alamat                = $request->alamat;
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

    public function UpJadwal(Request $request)
    {
        $id = JWTAuth::user()->id;

        $validator = Validator::make($request->all(), [
            'jadwal' => 'required',
            'user'   => 'required',
        ], [
            'jadwal.required' => 'Jadwal wajib diisi.',
            'user.required'   => 'TIdak murid yang di pilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = $request->user;
        for ($i = 0; $i < count($user); $i++) {
            $present                    = new StudentPresent;
            $present->student_id        = $user[$i];
            $present->unit_schedules_id = $request->jadwal;
            $present->save();
        }

        return response()->json(['status' => true], 200);
    }

    public function report()
    {
        $id   = JWTAuth::user()->id;
        $item = Report::where('user_id', $id)->latest()->get();
        return response()->json($item);
    }

    public function payment()
    {
        $item = Payment::latest()->get();
        return response()->json($item);
    }

    public function Ureport(Request $request)
    {
        $id = JWTAuth::user()->id;

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $re          = new Report;
        $re->user_id = $id;
        $re->reason  = $request->reason;
        $re->save();

        return response()->json(['status' => true], 200);
    }

    public function jadwal()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        if ($role == 3) {
            $da        = Teach::where('user', $id)->first();
            $items     = Head::where('unit', $da->unit_id)->with('jadwal:id,name,day,parse,start,end', 'murid:id,name', 'murid.present')->get();
            $allJadwal = collect();
            $allMurid  = collect();

            foreach ($items as $head) {
                $jadwalWithoutPivot = $head->jadwal->map(function ($j) {
                    return $j->makeHidden('pivot');
                });

                $allJadwal = $allJadwal->merge($jadwalWithoutPivot);
                $allMurid->push($head->murid);
            }

            return response()->json([
                'jadwal' => $allJadwal->values()->unique('id')->all(),
                'murid'  => $allMurid->values()->unique('id')->all(),
            ]);
        } else {
            $da    = Student::where('user', $id)->first();
            $items = Head::where('students', $da->id)->has('jadwal')->with('jadwal:id,name,day,parse,start,end', 'murid:id,name', 'murid.present')->first();
            if ($items) {
                $items->jadwal->makeHidden('pivot');
                return response()->json([
                    'jadwal' => $items->jadwal,
                    'murid'  => $items->murid,
                ]);
            } else {
                return response()->json([
                    'jadwal' => [],
                    'murid'  => [],
                ]);
            }

        }

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

        $res = Student::select('id', 'name', 'gender', 'user')
            ->with(
                'reg:id,students,price,unit,number,program',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                'reg.bill:id,head,time,bulan,tahun,via,status,first',
                'reg.lay.product.item'
            );

        if ($role == 2) {
            $student = $res->where('user', $id)->first();
            foreach ($student->reg as $val) {

                $bill = $val->bill->map(function ($a) use ($val) {
                    return [
                        "tipe"       => 0,
                        "id"         => $a->id,
                        'price'      => (int) $val->product->harga,
                        'total'      => $a->total,
                        "keterangan" => "Tagihan Bulan " . $a->bulan,
                        "kit"        => $a->kit,
                        "status"     => $a->status,
                        "via"        => $a->via,   
                    ];
                })->toArray();

                $lay = $val->lay->map(function ($a) {
                    return [
                        "id"         => $a->id,
                        "tipe"       => 1,
                        'price'      => (int) $a->product->harga,
                        'total'      => (int) $a->product->harga,
                        "keterangan" => $a->product->item->name,
                        "kit"        => null,
                        "status"     => $a->status,
                        "via"        => $a->via,  
                    ];
                })->toArray();

                $item[] = [
                    "program" => $val->product->program->name,
                    "kelas"   => $val->product->class->name,
                    "bill"    => array_merge($bill, $lay),
                ];

            }

            $result = [
                'name' => $student->name,
                "data" => $item,
            ];
            return response()->json($result);
        }

        if ($role == 3) {
            $guru     = Teach::where('user', $id)->first();
            $unit     = $guru->unit_id;
            $students = $res->whereHas('reg.units', function ($q) use ($unit) {
                $q->where('unit', $unit);
            })->get();

            $grouped = [];
            foreach ($students as $student) {
                foreach ($student->reg as $reg) {
                    $programName = $reg->programs->name ?? 'Unknown Program';
                    $className   = $reg->units->kelas[0]->name ?? 'Unknown Class';

                    $key = $programName . '|' . $className;

                    if (! isset($grouped[$key])) {
                        $grouped[$key] = [
                            'program'  => $programName,
                            'class'    => $className,
                            'students' => [],
                        ];
                    }

                    $bills = collect($reg->bill)->map(function ($bill) use ($reg) {
                        return [
                            'total'      => $bill->total,
                            'price'      => (int) $reg->product->harga,
                            'status'     => $bill->status,
                            "keterangan" => "Tagihan Bulan " . $bill->bulan,
                            "kit"        => $bill->kit,
                        ];
                    })->toArray();

                    $lay = collect($reg->lay)->map(function ($a) {
                        return [
                            'price'      => (int) $a->product->harga,
                            'total'      => (int) $a->product->harga,
                            "keterangan" => $a->product->item->name,
                            "kit"        => null,
                            "status"     => $a->status,
                        ];
                    })->toArray();

                    $grouped[$key]['students'][] = [
                        'name'  => $student->name,
                        'bills' => array_merge($bills, $lay),
                    ];
                }
            }
            return response()->json(array_values($grouped));

        }
    }

    public function Uplevel(Request $request)
    {
        // return response()->json($request->head);
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ], [
            'user.required' => 'TIdak murid yang di pilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $levels = Level::where('student_id', $request->user)
            ->where('head', $request->head)
            ->first();
        if ($levels) {
            $level             = new Level;
            $level->student_id = $levels->student_id;
            $level->teach_user = JWTAuth::user()->id;
            $level->level      = $levels->level + 1;
            $level->head       = $request->head;
            $level->note       = $request->note;
            $level->save();

            return response()->json(["status" => true], 200);
        } else {
            return response()->json(['errors' => "user tidak valid", "status" => false], 400);
        }

    }

    public function level()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        $res = Student::select('id', 'name', 'gender', 'user')
            ->with(
                'reg:id,students,price,unit,number,program',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                'reg.level',
            );

        if ($role == 2) {
            $head = Head::whereHas('murid', function ($q) use ($id) {
                $q->where('user', $id);
            })
                ->with('level', 'class')
                ->get();


            foreach($head as $val)
            {
                $da[] = [
                        "program"=>$val->programs->name,
                        "kelas"=>$val->class->name,
                        "level"=>$val->level->select('id',"level","status","note")->toArray()
                ];
            }
            return response()->json($da);
        }

        if ($role == 3) {
            $guru     = Teach::where('user', $id)->first();
            $unit     = $guru->unit_id;
            $students = $res->whereHas('reg.units', function ($q) use ($unit) {
                $q->where('unit', $unit);
            })->get();

            $grouped = [];

            foreach ($students as $student) {
                foreach ($student->reg as $reg) {
                    $programName = $reg->programs->name ?? 'Unknown Program';
                    $className   = $reg->units->kelas[0]->name ?? 'Unknown Class';

                    $key = $programName . '|' . $className;

                    if (! isset($grouped[$key])) {
                        $grouped[$key] = [
                            'head'     => $reg->id,
                            'program'  => $programName,
                            'class'    => $className,
                            'students' => [],
                        ];
                    }

                    $levels = collect($reg->level)->map(function ($item) {
                        return [
                            'level'  => $item->level,
                            'status' => $item->status,
                            'note'   => $item->note,
                        ];
                    })->toArray();

                    $grouped[$key]['students'][] = [
                        'id'    => $student->id,
                        'name'  => $student->name,
                        'level' => $levels,
                    ];
                }
            }

            return response()->json(array_values($grouped));

        }
    }

    public function tagihan()
    {
        $id = JWTAuth::user()->id;

        $student = Student::where('user', $id)
            ->with('reg.bill','reg.lay.product.item')
            ->first();

        $bill          = [];
        $hasStatusZero = false;


        foreach ($student->reg as $val) {


            foreach ($val->bill as $a) {
                if ($a->status != 1 ) {
                    $hasStatusZero = true;
                    $bill[]        = [
                        'total'      => (int) $a->total,
                        "keterangan" => "Anda punya tagihan bulan " . $a->bulan,
                        "status"     => $a->status,
                    ];
                }
            }

            foreach ($val->lay as $a) {
                if ($a->status != 1 ) {
                    $hasStatusZero = true;
                    $bill[]        = [
                        'total'      => (int) $a->product->harga,
                        "keterangan" => "Anda punya tagihan ".$a->product->item->name,
                        "status"     => $a->status,
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
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        if ($role == 2) {
            $student = User::select('id', 'name', 'email', 'status', 'role')
                ->with('data')
                ->where('id', $id)->first();
            $induk          = optional($student->data->reg->first())->induk;
            $induk          = $induk ? substr($induk, 0, -3) : null;
            $student->induk = $induk;
            $student->data->makeHidden('reg');

            return response()->json($student);
        }

        if ($role == 3) {
            $student = User::select('id', 'name', 'email', 'status', 'role')->where('id', $id)->first();
            return response()->json($student);
        }

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

            $level             = new Level;
            $level->student_id = $siswa->id;
            $level->head       = $head->id;
            $level->status     = 1;
            $level->save();

            $to       = '62' . substr($user->nomor, 1);
            $response = Http::post('http://192.168.18.22:3000/api/send', [
                'number'  => env('NumberWa'),
                'to'      => $to,
                'message' => "Selamat Anda Berhasil mendaftar\nPassword akun anda : murik@",
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
