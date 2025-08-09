<?php
namespace App\Services\Midtrans;

use App\Models\Paid;
use Midtrans\Config;
use Midtrans\Snap;

class Transaction
{
    public function __construct()
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey    = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false;
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    public static function create($params)
    {
        try {
            $snap        = Snap::createTransaction($params);
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

    public static function hook($data)
    {
        $serverKey    = config('midtrans.server_key');
        $signatureKey = hash('sha512',
            $data->order_id .
            $data->status_code .
            $data->gross_amount .
            $serverKey
        );

        if ($signatureKey !== $data->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }
        
        $order = Paid::where('mid', $data->order_id)->first();
        if ($data->transaction_status === 'pending') {
            if ($order) {
                $order->status = 2;
                $order->method = $data->all();
                $order->save();
            }
        }

        if ($data->transaction_status === 'settlement') {
            if ($order) {
                $order->status = 1;
                $order->save();
            }
        }

        return response()->json(['message' => 'Notification handled'], 200);
    }
}
