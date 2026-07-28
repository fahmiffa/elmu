<?php

namespace App\Jobs;

use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ExportStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 menit max

    protected string $filename;
    protected ?string $grade;
    protected ?string $kelas;
    protected ?string $unit;
    protected ?string $program;

    public function __construct(string $filename, $grade = null, $kelas = null, $unit = null, $program = null)
    {
        $this->filename = $filename;
        $this->grade    = $grade;
        $this->kelas    = $kelas;
        $this->unit     = $unit;
        $this->program  = $program;
    }

    public function handle(): void
    {
        // Tandai sebagai "sedang diproses"
        Cache::put("export_status_{$this->filename}", 'processing', now()->addHours(2));

        try {
            $export = new StudentExport($this->grade, $this->kelas, $this->unit, $this->program);

            // Pastikan direktori ada
            $dir = storage_path('app/public/exports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            Excel::store($export, "public/exports/{$this->filename}");

            // Tandai sebagai selesai
            Cache::put("export_status_{$this->filename}", 'done', now()->addHours(1));
        } catch (\Throwable $e) {
            Cache::put("export_status_{$this->filename}", 'error: ' . $e->getMessage(), now()->addHour());
            throw $e;
        }
    }
}
