<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMeetNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:meet
                            {--student_id= : Sinkronisasi hanya untuk student_id tertentu (opsional)}
                            {--dry-run : Tampilkan perubahan tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi field meet pada tabel student_presents berdasarkan urutan created_at (ascending) per student_id';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun    = $this->option('dry-run');
        $studentId = $this->option('student_id');

        $this->info('=== Sync Meet Number ===');
        if ($dryRun) {
            $this->warn('[DRY RUN] Tidak ada data yang akan disimpan.');
        }

        // Ambil semua student_id yang perlu diproses
        $query = DB::table('student_presents')->select('student_id')->distinct();
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        $students = $query->orderBy('student_id')->pluck('student_id');

        if ($students->isEmpty()) {
            $this->warn('Tidak ada data student_presents yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Total siswa yang akan diproses: {$students->count()}");
        $this->newLine();

        $totalUpdated = 0;
        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $sid) {
            // Ambil semua record untuk student ini, diurutkan ascending created_at
            $records = DB::table('student_presents')
                ->where('student_id', $sid)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc') // tiebreaker jika created_at sama
                ->select('id', 'meet', 'created_at')
                ->get();

            $meetCounter = 1;
            foreach ($records as $record) {
                if ($record->meet !== $meetCounter) {
                    if (!$dryRun) {
                        DB::table('student_presents')
                            ->where('id', $record->id)
                            ->update(['meet' => $meetCounter]);
                    }
                    $totalUpdated++;
                }
                $meetCounter++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("Total record yang AKAN diupdate: {$totalUpdated}");
        } else {
            $this->info("Total record yang berhasil diupdate: {$totalUpdated}");
        }

        $this->info('Selesai!');
        return self::SUCCESS;
    }
}
