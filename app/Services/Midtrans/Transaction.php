<?php
namespace App\Services\Midtrans;

use App\Models\Paid;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use App\Services\Firebase\FirebaseMessage;

class Transaction
{

    public static function create($params)
    {

        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey    = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MODE_MIDTRANS');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

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
            $data['order_id'] .
            $data['status_code'] .
            $data['gross_amount'] .
            $serverKey
        );

        if ($signatureKey !== $data['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }
        
        $order = Paid::where('mid', $data['order_id'])->first();
        if(!$order)
        {
            $order = Order::where('mid', $data['order_id'])->first();
        }

        if ($data['transaction_status'] === 'pending') {
            if ($order) {
                $order->status = 2;
                $order->via    = json_encode($data['va_numbers']);
                $order->save();
            }
        }

        if ($data['transaction_status'] === 'settlement') {
            if ($order) {
                $order->status = 1;
                $order->save();

                $kit   = $order->kit ? $order->kit->price->harga : 0;
                $harga = $order->reg->product->harga + $kit;
                $fcm   = $order->reg->murid->users->fcm;

                $message = [
                    "message" => [
                        "token"        => $fcm,
                        "notification" => [
                            "title" => "Tagihan",
                            "body"  => "Tagihan Anda bulan " . $order->bulan." sudah terbayar",
                        ],
                    ],
                ];
                FirebaseMessage::sendFCMMessage($message);
            }
        }

        return response()->json(['message' => 'Notification handled'], 200);
    }

}
