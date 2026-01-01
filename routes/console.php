<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BulkInsertJob;
use App\Jobs\SendFcm;
use App\Models\Head;
use App\Models\Paid;

Schedule::call(function () {
      // Log::info('Scheduler running: menulis log setiap menit');
})->everyMinute();

Schedule::call(function () {
      Log::info('Scheduler jalan tanggal 2 jam 8 pagi!');
      $head = Head::select('id', 'old')
            ->whereHas('kontrak',function($q){
                  $q->where('month',1);
            })
            ->where("done", 0)->get();
      foreach ($head as $val) {
            $paid = Paid::where('bulan', date("m"))->where('tahun', date("Y"))->where('head', $val->id)->exists();
            if ($paid == false) {
                  $da[] = ['head' => $val->id, 'bulan' => date("m"), 'tahun' => date("Y"), 'first' => $val->old == 0 ? 1 : 0];
                  $fcm = $val->murid->users->fcm ?? null;
                  if($fcm)
                  {
                        $message = [
                              "message" => [
                                    "token"        => $fcm,
                                    "notification" => [
                                    "title" => "Tagihan",
                                    "body"  => "Anda punya tagihan bulan" . date("m"),
                                    ],
                              ],
                        ];
                  
                        Log::info('Send fcm');
                        SendFcm::dispatch($message);
                  }
            }
      }

      if (count($da) > 0) {
            BulkInsertJob::dispatch($da);
      }
})->monthlyOn(1, '21:36');