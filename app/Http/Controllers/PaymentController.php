<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Payment::all();
        return view('master.payment.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Pembayaran";
        return view('master.payment.form', compact('action'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'per'  => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $item        = new Payment;
        $item->name  = $request->name;
        $item->month = $request->per;
        $item->save();

        return redirect()->route('dashboard.master.payment.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $items  = $payment;
        $action = "Edit Payment";
        return view('master.payment.form', compact('items', 'action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'name' => 'required',
            'per'  => 'required',
        ], [
            'required' => 'Field wajib diisi.',
        ]);

        $item        = $payment;
        $item->name  = $request->name;
        $item->month = $request->per;
        $item->save();

        return redirect()->route('dashboard.master.payment.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            // Cek apakah payment sudah digunakan di data murid (Head)
            $used = Head::where('payment', $payment->id)->exists();

            if ($used) {
                $message = 'Kontrak tidak dapat dihapus karena sudah digunakan oleh murid.';
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => 'error', 'message' => $message], 422);
                }
                return back()->with('err', $message);
            }

            $payment->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Data pembayaran berhasil dihapus.']);
            }

            return redirect()->route('dashboard.master.payment.index')->with('status', 'Data pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            return back()->with('err', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
