<?php

namespace App\Services\Firebase;

use Illuminate\Http\Request;
use Google_Client;
use Illuminate\Support\Facades\Http;

class FirebaseMessage 
{
    public static function sendFCMMessage()
    {
        // Path ke file service account JSON (letakkan di storage/app/firebase)
        $serviceAccountPath = storage_path(env('FIREBASE_CREDENTIALS'));

        // Baca file JSON dan decode
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        // Ambil project_id
        $projectId = $serviceAccount['project_id'];

        // Setup Google Client
        $client = new Google_Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope("https://www.googleapis.com/auth/firebase.messaging");

        // Ambil access token
        $token = $client->fetchAccessTokenWithAssertion()["access_token"];

        // Endpoint FCM v1 API
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Token device target (ubah sesuai token device target kamu)
        $deviceToken = "dsgEYr5YTIi2vZsbdk_1Ll:APA91bE7DHv6u8tWGm2XnytkKzU3yVK_gfIfm0Wqx3bkyEk4ijfXdD8MbTEVpHUxp7SN85Wb9yV3to6DbZurG4uY2ylXYIUklJgg9arKu_aOeBfiQ_Rii_U";

        // Payload pesan
        $message = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => "Notifikasi",
                    "body" => "Isi dari Laravel"
                ],
                "data" => [
                    "customData" => "12345"
                ]
            ]
        ];

        // Kirim request menggunakan Laravel HTTP client
        $response = Http::withToken($token)
            ->acceptJson()
            ->post($url, $message);

        if ($response->successful()) {
            return response()->json([
                'message' => 'Pesan berhasil dikirim',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Gagal mengirim pesan',
                'status' => $response->status(),
                'response' => $response->body()
            ], $response->status());
        }
    }
}
