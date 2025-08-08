<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Paid;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false; // Sandbox mode
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        
        
        try {
            $kode = date("YmdHis");
            $order = Paid::where('id',$request->id)->first();
            $order->mid = $kode;
            $order->save();
            
            $params = [
                'transaction_details' => [
                    'order_id' => $order->mid,
                    'gross_amount' => 60000,
                ],
                'credit_card' => [
                    'secure' => true,
                ],
                'customer_details' => [
                    'first_name' => 'Nama',
                    'last_name' => 'Pembeli',
                    'email' => 'email@example.com',
                    'phone' => '08123456789',
                ],
            ];

            $snap = Snap::createTransaction($params);
            $redirectUrl = $snap->redirect_url;

            return response()->json([
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
