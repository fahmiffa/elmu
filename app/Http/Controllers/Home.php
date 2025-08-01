<?php
namespace App\Http\Controllers;

use App\Jobs\BulkInsertJob;
use App\Models\App;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PDF;

class Home extends Controller
{

    public function bill(Request $request)
    {

        $bulan = $request->input('bulan');
        $da    = [];

        $head = Head::select('id')->get();
        foreach ($head as $val) {
            $paid = Paid::where('bulan', $bulan)->where('tahun', date("Y"))->where('head', $val->id)->exists();
            if ($paid == false) {
                $da[] = ['head' => $val->id, 'bulan' => $bulan, 'tahun' => date("Y")];
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
        $items = Head::with('murid')->with('paket')->with('kontrak')->with('class')->get();
        return view('reg.index', compact('items'));
    }

    public function invoice($id)
    {
        $paid = Paid::findOrFail($id);
        $pdf  = PDF::loadView('invoice', [
            'items' => $paid,
        ]);

        return $pdf->stream('invoice.pdf');
    }

    public function AddReg()
    {
        $kelas   = Kelas::all();
        $paket   = Program::all();
        $kontrak = Payment::all();
        $action  = "Form Pendaftaran";
        return view('reg.form', compact('action', 'kelas', 'kontrak', 'paket'));
    }

    public function regStore(Request $request)
    {
        $validated = $request->validate([
            // Wajib diisi
            'grade'                 => 'required|string|in:pra_tk,tk,sd,smp,sma',
            'kelas'                 => 'required|string',
            'gender'                => 'required|in:1,2',
            'place'                 => 'required|string',
            'birth'                 => 'required|date',
            'dad'                   => 'required|string',
            'dadJob'                => 'required|string',
            'mom'                   => 'required|string',
            'momJob'                => 'required|string',
            'hp_parent'             => 'required|string',
            'kontrak'               => 'required',
            'paket'                 => 'required',

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

            // Simpan ke tabel students
            $siswa                        = new Student;
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
            $head->kelas    = $request->kelas;
            $head->program  = $request->paket;
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
        $items = Paid::has('reg')->with('reg.murid', 'reg.paket', 'reg.class', 'reg.kontrak')->get();
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
