<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BulkInsertJob;
use App\Jobs\SendFcm;
use App\Models\Head;
use App\Models\Paid;

Schedule::call(function () {})->everyMinute();

Schedule::call(function () {
      $head = Head::select('id', 'old')
            ->whereHas('kontrak', function ($q) {
                  $q->where('month', 1);
            })
            ->with('murid.users')
            ->where("done", 0)->get();

      $da  = [];
      $now = now();
      $month = date("m");
      $year  = date("Y");

      foreach ($head as $val) {
            $paid = Paid::where('bulan', $month)->where('tahun', $year)->where('head', $val->id)->exists();
            if ($paid == false) {
                  $da[] = [
                        'head'       => $val->id,
                        'bulan'      => $month,
                        'tahun'      => $year,
                        'first'      => $val->old == 0 ? 1 : 0,
                        'created_at' => $now,
                        'updated_at' => $now
                  ];

                  $fcm = $val->murid->users->fcm ?? null;
                  if ($fcm) {
                        $message = [
                              "message" => [
                                    "token"        => $fcm,
                                    "notification" => [
                                          "title" => "Tagihan",
                                          "body"  => "Anda punya tagihan bulan" . $month,
                                    ],
                              ],
                        ];

                        Log::info('Send fcm for head ' . $val->id);
                        SendFcm::dispatch($message);
                  }
            }
      }

      if (count($da) > 0) {
            BulkInsertJob::dispatch($da);
      }
})->monthlyOn(1, '21:36');
