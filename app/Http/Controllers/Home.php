<?php
namespace App\Http\Controllers;

use App\Jobs\BulkInsertJob;
use App\Models\Addon;
use App\Models\App;
use App\Models\Grade;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Order;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\Price;
use App\Models\Program;
use App\Models\Student;
use App\Models\Unit;
use App\Models\UnitKelas;
use App\Models\User;
use App\Services\Firebase\FirebaseMessage;
use App\Services\Midtrans\Transaction;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDF;

class Home extends Controller
{
    public function absensi()
    {
        $items = Head::has('present')->with('jadwal:id,name,day,parse,start,end', 'murid:id,name', 'present')->get();
        return view('home.present.index', compact('items'));
    }

    public function level()
    {
        $items = Head::has('level')->with('murid:id,name', 'level.guru')->get();
        $lay   = Addon::where('first', 0)->with('price')->latest()->get();
        return view('home.level.index', compact('items', 'lay'));
    }

    public function send(Request $request, $id, $par)
    {
        try {

            if($par == "bul")
            {
                $order = Paid::where(DB::raw('md5(id)'), $request->id)->firstOrFail();
                $fcm   = $order->reg->murid->users->fcm;
                $billname = "bulan ".$order->bulan;
            }

            if($par == "lay")
            {
                $order = Order::where(DB::raw('md5(id)'), $id)->firstOrFail();
                $fcm   = $order->reg->murid->users->fcm;
                $billname = $order->product->item->name;
            }

            $message = [
                "message" => [
                    "token"        => $fcm,
                    "notification" => [
                        "title" => "Tagihan",
                        "body"  => "Anda punya tagihan " . $billname,
                    ],
                ],
            ];
            FirebaseMessage::sendFCMMessage($message);
            return back();
        } catch (\Exception $e) {
            return back();
        }
    }

    public function midtransPay(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id'   => 'required',
            'tipe' => 'required',
        ], [
            'required' => ':attribute wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        try {

            if ($request->tipe == 0) {
                $kode = 'trx-m'.date("YmdHis");
                $order      = Paid::where('id', $request->id)->first();
                $order->mid = $kode;
                $order->save();
                $total  = $order->total;
                $name   = $order->reg->murid->name;
                $email  = $order->reg->murid->users->email;
                $hp     = $order->reg->murid->users->hp;
                $parent = $order->reg->murid->dad . '' . $order->reg->murid->mom;
                $mid    = $order->mid;
                $des    = "Tagihan Bulan " . $order->bulan;
            } else {
                $kode = 'trx-o'.date("YmdHis");
                $lay      = Order::where('id', $request->id)->firstOrFail();
                $lay->mid = $kode;
                $lay->save();
                $name   = $lay->reg->murid->name;
                $email  = $lay->reg->murid->users->email;
                $hp     = $lay->reg->murid->users->hp;
                $parent = $lay->reg->murid->dad . '' . $lay->reg->murid->mom;
                $total  = $lay->product->harga;
                $mid    = $lay->mid;
                $des    = $lay->product->item->name;
            }

            $params = [
                'transaction_details' => [
                    'order_id'     => $mid,
                    'gross_amount' => $total,
                ],
                'item_details'        => [
                    [
                        'id'       => 'item01',
                        'price'    => $total,
                        'quantity' => 1,
                        'name'     => $des,
                    ],
                ],
                'credit_card'         => [
                    'secure' => true,
                ],
                'customer_details'    => [
                    'first_name' => $name,
                    'last_name'  => $parent,
                    'email'      => $email,
                    'phone'      => $hp,
                ],
                "callbacks"=> []
            ];

            return Transaction::create($params);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }

    }

    public function midtransHook(Request $request)
    {
        Log::info('Midtrans Webhook Received:', $request->all());
        $data = $request->all();
        return Transaction::hook($data);

    }

    public function user()
    {
        $items = User::with('data')->get();
        return view('master.user.index', compact('items'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();
        return back();
    }

    public function userEdit($id)
    {
        $user = User::where(DB::raw('md5(id)'), $id)->firstOrFail();
        if ($user->role != 0) {
            return view('master.user.detail', compact('user'));
        } else {
            return back();
        }
    }

    public function layanan(Request $request, $id)
    {
        $head           = Head::where(DB::raw('md5(students)'), $id)->firstOrFail();
        $done = $request->has("done");
        $fcm =  $head->murid->users->fcm;
        if($done)
        {
            $message = [
                "message" => [
                    "token"        => $fcm,
                    "notification" => [
                        "title" => "informasi",
                        "body"  => "Anda telah menyelesaiakan program Belajar ".$head->paket->name,
                    ],
                ],
            ];    

            $head->done = 1;
            $head->save();
        }
        else
            {
            $order          = new Order;
            $order->head    = $head->id;
            $order->student = $head->students;
            $order->price   = $request->book;
            $order->save();
    
            $message = [
                "message" => [
                    "token"        => $fcm,
                    "notification" => [
                        "title" => "Tagihan",
                        "body"  => "Anda punya tagihan " . $order->product->item->name,
                    ],
                ],
            ];    

        }

        Level::where('id', $request->level)->update(['status' => 1]);

        if($fcm)
        {
            FirebaseMessage::sendFCMMessage($message);
        }

        return back()->with('status', 'Ugrade Level berhasil');
    }

    public function bill(Request $request)
    {

        $bulan = $request->input('bulan');
        $da    = [];

        $head = Head::select('id','old')
        // ->whereHas('kontrak',function($q){
        //     $q->where('month',1);
        // })
        ->where("done",0)->get();

        foreach ($head as $val) {
            $paid  = Paid::where('bulan', $bulan)->where('tahun', date("Y"))->where('head', $val->id)->exists();
            if ($paid == false) {
                $da[] = ['head' => $val->id, 'bulan' => $bulan, 'tahun' => date("Y"), 'first' => $val->old == 0 ? 1 : 0];
            }
        }


        if (count($da) > 0) {
            BulkInsertJob::dispatch($da);
        }

        return back();
    }

    public function index()
    {
        return view('home.index');
    }

    public function reg()
    {
        $items = Head::with('murid', 'class', 'programs', 'kontrak', 'units', 'level')->get();
        return view('home.reg.index', compact('items'));
    }

    public function invoice($id)
    {
        $paid = Paid::where(DB::raw('md5(id)'), $id)->firstOrFail();
        $pdf  = PDF::loadView('invoice', [
            'items' => $paid,
        ]);

        return $pdf->stream('invoice.pdf');
    }

    public function payment(Request $request, $id, $par)
    {
        if ($par == "bul") {
            $paid = Paid::where(DB::raw('md5(id)'), $id)->firstOrFail();
            if ($paid->status == 0) {
                $user = $paid->reg->murid->users;
                if ($user->status == 0) {
                    $user->status = 1;
                    $user->save();
                }
                $paid->status = 1;
                $paid->time   = date("Y-m-d H:i:s");
                $paid->ket    = $request->ket;
                $paid->via    = $request->via;
                $paid->grand  = $paid->total;
                $paid->save();
            }

            $fcm   = $paid->reg->murid->users->fcm;

            if ($fcm) {
                $message = [
                    "message" => [
                        "token"        => $fcm,
                        "notification" => [
                            "title" => "Tagihan",
                            "body"  => "Pembayaran Tagihan bulan " . $paid->bulan . " Berhasil",
                        ],
                    ],
                ];
                FirebaseMessage::sendFCMMessage($message);

            }

            return back()->with('status', 'Pembayaran berhasil');
        }

        if ($par == "lay") {
            $order = Order::where(DB::raw('md5(id)'), $id)->firstOrFail();

            if ($order->status == 0) {
                $order->status = 1;
                $order->time   = date("Y-m-d H:i:s");
                $order->ket    = $request->ket;
                $order->via    = $request->via;
                $order->save();
            }

            $fcm = $order->reg->murid->users->fcm;
            if ($fcm) {
                $message = [
                    "message" => [
                        "token"        => $fcm,
                        "notification" => [
                            "title" => "Tagihan",
                            "body"  => "Pembayaran Tagihan " . $order->product->item->name . " Berhasil",
                        ],
                    ],
                ];
                FirebaseMessage::sendFCMMessage($message);

            }
            return back()->with('status', 'Pembayaran berhasil');
        }
        return back()->with('err', 'Pembayaran gagal');
    }

    public function AddReg()
    {
        $kelas = Kelas::with('program:id,name','units:id,name')->get();
        $kontrak = Payment::all();
        $grade   = Grade::all();
        $action = "Form Pendaftaran";
        $head   = Head::has('murid')->get();
        return view('home.reg.form', compact('action', 'kelas', 'kontrak', 'grade', 'head'));
    }

    public function regStore(Request $request)
    {
        $validated = $request->validate([
            // Wajib diisi
            'murid'                 => "required_if:option,2",
            'grade'                 => 'required',
            'kelas'                 => ['required', Rule::in(Kelas::pluck('id')->toArray())],
            'gender'                => 'nullable|in:1,2',
            'place'                 => 'nullable|string',
            'birth'                 => 'nullable|date',
            'dad'                   => 'nullable|string',
            'dadJob'                => 'nullable|string',
            'mom'                   => 'nullable|string',
            'momJob'                => 'nullable|string',
            'hp_parent'             => 'nullable|string',
            'kontrak'               => 'required',
            'program'               => ['required', Rule::in(Program::pluck('id')->toArray())],
            'unit'                  => ['required', Rule::in(Unit::pluck('id')->toArray())],
            'email'                 => 'required_if:option,1|unique:users,email',

            // Optional
            'name'                  => 'required_if:option,1',
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
                'required'    => 'Field Wajib disi',
                'required_if' => 'Field Wajib disi',

            ]
        );

        DB::beginTransaction();

        try {
            if ($request->option == 1) {

                $path = null;
                if ($request->hasFile('image')) {
                    $path = $request->file('image')->store('images', 'public');
                }


                $user           = new User;
                $user->name     = UserName($request->name);
                $user->email    = $request->email;
                $user->role     = 2;
                $user->status   = 0;
                $user->password = bcrypt('murik@');
                $user->save();

                $siswa                        = new Student;
                $siswa->user                  = $user->id;
                $siswa->name                  = $request->name;
                $siswa->img                   = $path;
                $siswa->grade_id              = $request->grade;
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

                $price = Price::where('kelas', $request->kelas)
                    ->where('product', $request->program)
                    ->first();

                $head           = new Head;
                $head->number   = Head::where('unit', $request->unit)->count() + 1;
                $head->global   = Head::count() + 1;
                $head->students = $siswa->id;
                $head->tanggal  = date("Y-m-d");
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

            } else {

                $parent = Head::where('id', $request->murid)->firstOrFail();
                $price  = Price::where('kelas', $request->kelas)
                    ->where('product', $request->program)
                    ->first();

                $head           = new Head;
                $head->number   = Head::count() + 1;
                $head->parent   = $parent->id;
                $head->students = $parent->students;
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
                $level->student_id = $parent->students;
                $level->head       = $head->id;
                $level->status     = 1;
                $level->save();
            }

            DB::commit();

            return redirect()->route('dashboard.reg');

        } catch (\Exception $e) {
            DB::rollback();

            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function schedule()
    {
        $items = Paid::has('reg')->with('reg.murid', 'reg.paket', 'reg.class', 'reg.kontrak')->get();
        return view('home.schedule.index', compact('items'));
    }

    public function pay()
    {
        $items = Paid::has('reg')->with('reg.murid.users', 'reg.class', 'reg.programs', 'reg.kontrak', 'reg.units')->orderBy('bulan', 'asc')->get();
        $lay   = Order::has('product')->with('reg.murid.users', 'product.item')->get();
        return view('home.pay.index', compact('items', 'lay'));
    }

    public function master()
    {
        return view('master.index');
    }

    public function chart($par)
    {
        $bulanMap = [
            1 => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5 => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Aug',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $now   = (int) date('Y');
        $start = (int) env('APP_START');

        if ($par == 'reg') {
            $data = DB::table('head')
                ->selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan, count(*) as total')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                ->get();

            foreach ($data->pluck('tahun')->unique() as $tahun) {
                foreach ($bulanMap as $num => $namaBulan) {
                    $dummyData[$tahun][$namaBulan] = 0;
                }
            }

            foreach ($data as $item) {
                $tahun                     = $item->tahun;
                $bulan                     = $bulanMap[$item->bulan];
                $dummyData[$tahun][$bulan] = (int) $item->total;
            }

            $year = [];

            for ($tahun = $now; $tahun >= $start; $tahun--) {
                array_push($year, $tahun);
            }

            $da = [
                "Year" => $year,
                "data" => $dummyData,
            ];
        }

        if ($par == 'pay') {

            $data = DB::table('paids')
                ->selectRaw('tahun, bulan, status, count(*) as total')
                ->groupBy('bulan', 'tahun', 'status')
                ->orderBy('tahun', 'asc')
                ->orderBy('bulan', 'asc')
                ->get();

            foreach ($data->pluck('tahun')->unique() as $tahun) {
                foreach ($bulanMap as $num => $namaBulan) {
                    $dummyData[$tahun][$namaBulan] = [
                        'bayar' => 0,
                        'belum' => 0,
                    ];
                }
            }

            foreach ($data as $item) {
                $tahun     = $item->tahun;
                $bulan     = $bulanMap[$item->bulan];
                $statusKey = $item->status == 1 ? 'bayar' : 'belum';

                $dummyData[$tahun][$bulan][$statusKey] = (int) $item->total;
            }

            $year = [];

            for ($tahun = $now; $tahun >= $start; $tahun--) {
                array_push($year, $tahun);
            }

            $da = [
                "Year" => $year,
                "data" => $dummyData,
            ];
        }

        return response()->json($da, 200);
    }

    public function setting()
    {

        return view('setting');
    }

    public function pass(Request $request)
    {

        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|same:password_confirmation',
            'password_confirmation' => 'required',
        ], [
            'current_password.required'      => 'Password sekarang wajib diisi.',
            'new_password.required'          => 'Password baru wajib diisi.',
            'new_password.min'               => 'Password baru minimal 8 karakter.',
            'new_password.same'              => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password Sekarang salah']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

}
