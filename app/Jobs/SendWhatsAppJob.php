<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to;
    public $message;

    public $tries = 3;
    public $timeout = 15;

    public function __construct($to, $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    public function handle()
    {
        try {
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->post(env('URL_WA') . '/send', [
                    'number'  => env('NUMBER_WA'),
                    'to'      => $this->to,
                    'message' => $this->message,
                ]);

            if (!$response->successful()) {
                Log::error('WA send failed', [
                    'body' => $response->body(),
                ]);

                throw new \Exception('WA API failed');
            }

        } catch (\Throwable $e) {
            Log::error('WA Job Error: ' . $e->getMessage());
            throw $e;
        }
    }
}