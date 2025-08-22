<?php
namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Kelas;
use App\Models\Paid;
use App\Models\Price;
use App\Models\Program;
use App\Models\Student;
use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{

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
            ->with('units:id,name,pic,hp', 'program:id,name')
            ->get()
            ->each(function ($items) {
                $items->units->each->makeHidden('pivot');
                $items->program->each->makeHidden('pivot');
            });
        $grades = [
            'pra_tk' => 'Pra TK',
            'tk'     => 'TK',
            'sd'     => 'SD',
            'smp'    => 'SMP',
            'alumni' => 'Alumni',
        ];
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
        $id      = JWTAuth::user()->id;
        $Student = Student::where('user', $id)
            ->select('id', 'name', 'img', 'user', 'gender')
            ->with(
                'reg:id,students,price,unit,number',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                // 'reg.units:id,name',
                'reg.bill:id,head,time,bulan,tahun,via,status,first',
                // 'users:id,name,role,status'
                'reg.jadwal:id,head,teach_id,status',
                'reg.jadwal.guru:id,name,hp',
                'reg.jadwal.meet:id,name,schedule_id',
                'reg.jadwal.meet.waktu:id,schedule_meet_id,waktu,status'
            )
            ->first();
        return response()->json($Student);
    }

    public function data()
    {
        $id      = JWTAuth::user()->id;
        $Student = User::select('id', 'name', 'email', 'status', 'role')->where('id', $id)->first();
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

        // $id = JWTAuth::user()->id;

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
            'grade'                 => 'required|string|in:pra_tk,tk,sd,smp,sma',
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
            'email'                 => 'required|email|unique:users,email',
            // 'hp'                    => 'required',

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
            $user->role     = 2;
            $user->status   = 0;
            $user->password = bcrypt('murik@');
            $user->save();

            $siswa                        = new Student;
            $siswa->user                  = $user->id;
            $siswa->name                  = $request->name;
            $siswa->img                   = $path;
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

            $unit = Head::where('unit', $request->unit)->count() + 1;

            $head           = new Head;
            $head->number   = $unit;
            $head->students = $siswa->id;
            $head->unit     = $request->unit;
            $head->price    = $request->program;
            $head->payment  = $request->kontrak;
            $head->save();

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
