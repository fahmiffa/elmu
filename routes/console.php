<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::call(function () {
      Log::info('Scheduler running: menulis log setiap menit');
})->everyMinute();

Schedule::call(function () {
      \Log::info('Scheduler jalan tanggal 24 jam 8 pagi!');
})->monthlyOn(24, '08:00');