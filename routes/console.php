<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BulkInsertJob;

Schedule::call(function () {
      // Log::info('Scheduler running: menulis log setiap menit');
})->everyMinute();

Schedule::call(function () {
      Log::info('Scheduler jalan tanggal 24 jam 8 pagi!');
})->monthlyOn(28, '08:00');