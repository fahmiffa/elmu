<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BulkInsertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        try {
        \Log::info('⏳ Proses insert dimulai...');
        DB::table('paids')->insert($this->data);
        \Log::info('✅ Insert berhasil');
    } catch (\Exception $e) {
        \Log::error('Insert gagal: '.$e->getMessage());
        throw $e; 
    }
    }
}
