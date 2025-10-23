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
use App\Models\Raport;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentPresent;
use App\Models\Teach;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vidoes;
use App\Rules\NumberWa;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            $user        = $siswa->users;
            $user->email = $request->email;
            $user->save();

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
        $item = Report::select('reason', 'reply')->where('user', $id)->latest()->get();
        return response()->json($item);
    }

    public function raport()
    {
        $id   = JWTAuth::user()->id;
        $item = Raport::select('name', 'file')->where('student_id', $id)->latest()->get()
            ->map(function ($q) {
                return ['name' => $q->name, "url" => asset('storage/' . $q->file)];
            });
        return response()->json($item);
    }

    public function payment()
    {
        $item = Payment::latest()->get();
        return response()->json($item);
    }

    public function ureport(Request $request)
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

        $check = Report::where('user', $id)->whereNull('reply');

        if ($check->exists()) {
            return response()->json(['errors' => ['message' => 'Laporan masih dalam proses']], 400);
        }
        $re         = new Report;
        $re->user   = $id;
        $re->reason = $request->reason;
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

    public function videos()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        if ($role == 3) {
            $items = Vidoes::where('to', 3)->get();
        } else {
            $items = Vidoes::where('user', $id)->get();
        }

        $da = $items->map(function ($q) {
            return ['name' => $q->name, 'url' => asset('storage/' . $q->pile)];
        });
        return response()->json($da);
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
            'login'    => 'required', // bisa email atau name
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $login    = $request->input('login');
        $password = $request->input('password');

        // Cari user berdasarkan email atau name
        $user = \App\Models\User::where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (! $user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if ($user->status == 0) {
            return response()->json(['error' => 'Akun tidak aktif'], 403);
        }

        // Set credentials dengan email yang ditemukan
        $credentials = ['name' => $user->name, 'password' => $password];

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Simpan FCM jika ada
        if ($request->fcm) {
            $user->fcm = $request->fcm;
            $user->save();
        }

        return response()->json([
            'token'      => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 1,
            'role'       => $user->role,
            'uid'        => md5($user->id),
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
        $products = Program::select('id', 'name', 'des', 'level')->get();
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

    public function upass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old' => 'required',
            'new' => 'required',
        ], [
            'old.required' => 'Password lama Wajib diisi',
            'new.required' => 'Password baru Wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $id   = JWTAuth::user()->id;
        $data = User::where('id', $id)->first();

        if (! $data) {
            return response()->json(['errors' => ['message' => 'User tidak valid']], 400);
        }

        if (! Hash::check($request->old, $data->password)) {
            return response()->json(['errors' => ['message' => 'Password lama tidak valid']], 400);
        }

        $data->password = Hash::make($request->new);
        $data->save();

        return response()->json(['status' => true, "data" => $request->new], 200);
    }
    public function Uplevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'  => 'required',
            'head'  => 'required',
            'level' => 'required',
        ], [
            'user.required'  => 'Tidak murid yang dipilih',
            'head.required'  => 'Head diperlukan',
            'level.required' => 'Level diperlukan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $levelsQuery = Level::where('student_id', $request->user)
            ->where('head', $request->head);

        if (! $levelsQuery->exists()) {
            return response()->json(['errors' => ['message' => 'Murid tidak valid']], 400);
        }

        $upgradeInProcess = Level::where('student_id', $request->user)
            ->where('head', $request->head)
            ->where('status', 0)
            ->exists();

        if ($upgradeInProcess) {
            return response()->json(['errors' => ['message' => 'Murid dalam proses Upgrade']], 400);
        }

        $currentLevel = $levelsQuery->latest()->first();
        if (! $currentLevel) {
            return response()->json(['errors' => ['message' => 'Murid tidak valid', 'data' => $levelsQuery->first()]], 400);
        }

        if ($currentLevel->level >= $request->level) {
            return response()->json(['errors' => ['message' => 'Level Tidak Valid']], 400);
        }

        // Buat level baru untuk upgrade
        $newLevel             = new Level();
        $newLevel->student_id = $currentLevel->student_id;
        $newLevel->teach_user = JWTAuth::user()->id;
        $newLevel->level      = $request->level;
        $newLevel->head       = $request->head;
        $newLevel->note       = $request->note ?? null;
        $newLevel->save();

        return response()->json(['status' => true], 200);
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

            foreach ($head as $val) {
                $da[] = [
                    "program" => $val->programs->name,
                    "kelas"   => $val->class->name,
                    "level"   => $val->level->select('id', "level", "status", "note")->toArray(),
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
                        'head'  => $reg->id,
                        'id'    => $student->id,
                        'name'  => $student->name,
                        'name'  => $student->name,
                        'level' => $levels,
                    ];
                }
            }

            return response()->json(array_values($grouped));
            return response()->json($students);

        }
    }

    public function tagihan()
    {
        $id = JWTAuth::user()->id;

        $student = Student::where('user', $id)
            ->with('reg.bill', 'reg.lay.product.item')
            ->first();

        $bill          = [];
        $hasStatusZero = false;

        foreach ($student->reg as $val) {

            foreach ($val->bill as $a) {
                if ($a->status != 1) {
                    $hasStatusZero = true;
                    $bill[]        = [
                        'total'      => (int) $a->total,
                        "keterangan" => "Anda punya tagihan bulan " . $a->bulan,
                        "status"     => $a->status,
                    ];
                }
            }

            foreach ($val->lay as $a) {
                if ($a->status != 1) {
                    $hasStatusZero = true;
                    $bill[]        = [
                        'total'      => (int) $a->product->harga,
                        "keterangan" => "Anda punya tagihan " . $a->product->item->name,
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
            $head->number   = Head::where('unit', $request->unit)->count() + 1;
            $head->global   = Head::count() + 1;
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
            $response = Http::post('https://node.extra.my.id/api/send', [
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
