<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Home;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeachController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'loginForm'])->name('home')->middleware('guest');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('/', [Home::class, 'index'])->name('home');
    Route::get('/pendaftaran', [Home::class, 'reg'])->name('reg');
    Route::Post('/pendaftaran', [Home::class, 'regStore'])->name('reg.store');
    Route::get('/pendaftaran/tambah', [Home::class, 'AddReg'])->name('reg.create');
    Route::get('/pembayaran', [Home::class, 'pay'])->name('pay');
    Route::get('/penjadwalan', [Home::class, 'schedule'])->name('schedule');
    Route::get('setting', [Home::class, 'setting'])->name('setting');
    Route::post('/pass', [Home::class, 'pass'])->name('pass');
    Route::post('/bill', [Home::class, 'bill'])->name('bill');
    Route::get('/invoice/{id}', [Home::class, 'invoice'])->name('invoice');

    Route::get('/job-progress/{jobId}', function ($jobId) {
        // $total = DB::table('head')->where('bulan', $jobId)->count();
        $total = DB::table('head')->count();
        $done  = DB::table('paids')->where('bulan', $jobId)->where('status', 0)->count();

        $progress = $total > 0 ? round(($done / $total) * 100) : 0;
        return response()->json(['progress' => $progress]);
    });

    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/', [Home::class, 'master'])->name('index');
        Route::resource('unit', UnitController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('payment', PaymentController::class);
        Route::resource('teach', TeachController::class);
        Route::resource('student', StudentController::class);
        Route::resource('program', ProgramController::class);
    });
});
