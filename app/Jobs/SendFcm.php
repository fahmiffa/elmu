<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Firebase\FirebaseMessage;

class SendFcm implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(array $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        FirebaseMessage::sendFCMMessage($this->message);
    }
}
