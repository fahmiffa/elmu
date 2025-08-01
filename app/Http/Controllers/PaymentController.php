<?php
namespace App\Http\Controllers;

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
        return view('payment.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $action = "Tambah Pembayaran";
        return view('payment.form', compact('action'));
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
    public function show(Payment $payment)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $items  = $payment;
        $action = "Edit Payment";
        return view('payment.form', compact('items', 'action'));
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
        $payment->delete();
        return redirect()->route('dashboard.master.payment.index');
    }
}
