<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Paid;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Simpan log untuk debugging
        Log::info('Midtrans Webhook Received:', $request->all());

        // Ambil data dari notifikasi
        $serverKey = config('midtrans.server_key'); // atau langsung dari .env
        $signatureKey = hash('sha512', 
            $request->order_id . 
            $request->status_code . 
            $request->gross_amount . 
            $serverKey
        );

        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Contoh handle status pembayaran
        if ($request->transaction_status === 'pending') {
            // Update order status, misalnya:
            
            $order = Paid::where('mid',$request->order_id)->first();
            if ($order) {
                $order->status = 2;
                $order->method = $request->all();
                $order->save();
            }
        }

        if ($request->transaction_status === 'settlement') {
            // Update order status, misalnya:
            
            $order = Paid::where('mid',$request->order_id)->first();
            if ($order) {
                $order->status = 1;
                $order->save();
            }
        }

        return response()->json(['message' => 'Notification handled'], 200);
    }
}
