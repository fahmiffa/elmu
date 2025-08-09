<?php
namespace App\Http\Controllers;

use App\Jobs\BulkInsertJob;
use App\Models\App;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\Price;
use App\Models\Program;
use App\Models\Student;
use App\Models\Unit;
use App\Models\UnitKelas;
use App\Models\User;
use App\Services\Midtrans\Transaction;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PDF;

class Home extends Controller
{
    public function fcm()
    {
        return FirebaseMessage::sendFCMMessage();
    }

    public function midtransPay(Request $request)
    {

        $request->validate([
            'id' => 'required|integer',
        ]);

        try {

            $kode       = date("YmdHis");
            $order      = Paid::where('id', $request->id)->first();
            $order->mid = $kode;
            $order->save();
            $params = [
                'transaction_details' => [
                    'order_id'     => $order->mid,
                    'gross_amount' => $order->reg->product->harga,
                ],
                'credit_card'         => [
                    'secure' => true,
                ],
                'customer_details'    => [
                    'first_name' => $order->reg->murid->name,
                    'last_name'  => $order->reg->murid->dad . '' . $order->reg->murid->mom,
                    'email'      => $order->reg->murid->users->email,
                    'phone'      => $order->reg->murid->users->hp,
                ],
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
        $items = User::all();
        return view('user.index', compact('items'));
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
            return view('user.detail', compact('user'));
        } else {
            return back();
        }
    }

    public function bill(Request $request)
    {

        $bulan = $request->input('bulan');
        $da    = [];

        $head = Head::select('id')->get();
        foreach ($head as $val) {
            $first = Paid::where('head', $val->id)->exists();
            $paid  = Paid::where('bulan', $bulan)->where('tahun', date("Y"))->where('head', $val->id)->exists();
            if ($paid == false) {
                $da[] = ['head' => $val->id, 'bulan' => $bulan, 'tahun' => date("Y"), 'first' => ! $first ? 1 : 0];
            }
        }

        if (count($da) > 0) {
            BulkInsertJob::dispatch($da);
        }

        return back();
        // return response()->json(['job_id' => $bulan]);
    }

    public function index()
    {
        return view('home');
    }

    public function reg()
    {
        $items = Head::with('murid')->with('product.program', 'product.class')->with('kontrak')->with('units')->get();
        return view('reg.index', compact('items'));
    }

    public function invoice($id)
    {
        $paid = Paid::where(DB::raw('md5(id)'), $id)->firstOrFail();
        // dd($paid->reg->product);
        $pdf = PDF::loadView('invoice', [
            'items' => $paid,
        ]);

        return $pdf->stream('invoice.pdf');
    }

    public function payment(Request $request, $id)
    {
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
            $paid->save();
        }

        return back();
    }

    public function AddReg()
    {
        $kelas = Kelas::with('kelas_unit')->get();
        $paket = Price::select('id', 'harga', 'product', 'kelas')
            ->with('program:id,name')
            ->latest()
            ->get();
        $kontrak = Payment::all();
        $unit    = UnitKelas::select('id', 'kelas_id', 'unit_id')
            ->with('unit:id,name')
            ->get();
        $action = "Form Pendaftaran";
        return view('reg.form', compact('action', 'kelas', 'kontrak', 'paket', 'unit'));
    }

    public function regStore(Request $request)
    {
        $validated = $request->validate([
            // Wajib diisi
            'grade'                 => 'required|string|in:pra_tk,tk,sd,smp,sma',
            'kelas'                 => 'required|string',
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
            'email'                 => 'required',

            // Optional
            'induk'                 => 'nullable|string',
            'name'                  => 'nullable|string',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sekolah_kelas'         => 'nullable|string',
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
            // Upload image jika ada
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

            // Simpan ke tabel students
            $siswa                        = new Student;
            $siswa->user                  = $user->id;
            $siswa->name                  = $request->name;
            $siswa->img                   = $path;
            $siswa->induk                 = $request->induk;
            $siswa->jenjang               = $request->grade;
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

            // Simpan ke tabel head
            $head           = new Head;
            $head->students = $siswa->id;
            $head->unit     = $request->unit;
            $head->price    = $request->program;
            $head->payment  = $request->kontrak;
            $head->save();

            DB::commit();

            return redirect()->route('dashboard.reg');

        } catch (\Throwable $e) {
            DB::rollback();

            // Hapus gambar jika sempat ter-upload
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->withErrors('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function schedule()
    {
        $items = Paid::has('reg')->with('reg.murid', 'reg.paket', 'reg.class', 'reg.kontrak')->get();
        return view('schedule.index', compact('items'));
    }

    public function pay()
    {
        $items = Paid::has('reg')->with('reg.murid', 'reg.product.class', 'reg.product.program', 'reg.kontrak', 'reg.units')->orderBy('bulan', 'asc')->get();
        return view('pay.index', compact('items'));
    }

    public function master()
    {
        return view('master');
    }

    public function chart()
    {

        $data = DB::table('carts')
            ->selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(count) as total_penjualan')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get();

        $bulanMap = [
            1 => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5 => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Aug',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $dummyData = [];

        foreach ($data->pluck('tahun')->unique() as $tahun) {
            foreach ($bulanMap as $num => $namaBulan) {
                $dummyData[$tahun][$namaBulan] = 0;
            }
        }

        foreach ($data as $item) {
            $tahun                     = $item->tahun;
            $bulan                     = $bulanMap[$item->bulan];
            $dummyData[$tahun][$bulan] = (int) $item->total_penjualan;
        }

        $now   = (int) date('Y');
        $start = (int) env('APP_START');
        $year  = [];

        for ($tahun = $now; $tahun >= $start; $tahun--) {
            array_push($year, $tahun);
        }

        $da = [
            "Year" => $year,
            "data" => $dummyData,
        ];

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
