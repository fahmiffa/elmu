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
                if ($data['payment_type'] === "bank_transfer") {
                    $order->via    = json_encode($data['va_numbers']);
                    $order->status = 2;
                }

                if ($data['payment_type'] === "echannel") {
                    $order->via = json_encode([
                        'bank'      => "Mandiri",
                        'va_number' => $data['bill_key'],
                        'code'      => $data['biller_code'],
                    ]);
                    $order->status = 2;
                }

                $order->save();
            }
        }

        if ($data['transaction_status'] === 'expire') {
            if ($order) {
                $order->status = 3;
                $order->save();
            }
        }

        if ($data['transaction_status'] === 'settlement') {
            if ($order) {
                $order->status = 1;
                $order->save();

                $fcm   = $order->reg->murid->users->fcm;
                if($order->bulan)
                {
                    $message = [
                        "message" => [
                            "token"        => $fcm,
                            "notification" => [
                                "title" => "Tagihan",
                                "body"  => "Tagihan Anda bulan " . $order->bulan." sudah terbayar",
                            ],
                        ],
                    ];
                }
                else
                {
                    $billname = $order->product->item->name;
                    $message = [
                        "message" => [
                            "token"        => $fcm,
                            "notification" => [
                                "title" => "Tagihan",
                                "body"  => "Tagihan Anda  " . $billname." sudah terbayar",
                            ],
                        ],
                    ];

                }
                
                FirebaseMessage::sendFCMMessage($message);
            }
        }

        return response()->json(['message' => 'Notification handled'], 200);
    }

}
