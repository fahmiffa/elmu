<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Home;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeachController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    // Artisan::call('db:wipe');
    // Artisan::call('migrate');
    // Artisan::call('db:seed');
    Artisan::call('optimize:clear');
    File::put(storage_path('logs/laravel.log'), '');
    return 'Log cleared';
});

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginForm']);
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [Home::class, 'index'])->name('home');
    Route::get('/fierbase', [Home::class, 'fcm'])->name('fcm');
    Route::get('/pendaftaran', [Home::class, 'reg'])->name('reg');
    Route::Post('/pendaftaran', [Home::class, 'regStore'])->name('reg.store');
    Route::get('/pendaftaran/tambah', [Home::class, 'AddReg'])->name('reg.create');
    Route::get('/pembayaran', [Home::class, 'pay'])->name('pay');
    Route::post('/pembayaran/{id}', [Home::class, 'payment'])->name('payment');
    Route::get('/pembelajaran', [Home::class, 'study'])->name('study');
    Route::get('setting', [Home::class, 'setting'])->name('setting');
    Route::post('/pass', [Home::class, 'pass'])->name('pass');
    Route::post('/bill', [Home::class, 'bill'])->name('bill');
    Route::post('/send/{id}', [Home::class, 'send'])->name('send');
    Route::get('/invoice/{id}', [Home::class, 'invoice'])->name('invoice');
    Route::resource('jadwal', ScheduleController::class);

    Route::get('/job-progress/{jobId}', function ($jobId) {
        // $total = DB::table('head')->where('bulan', $jobId)->count();
        $total = DB::table('head')->count();
        $done  = DB::table('paids')->where('bulan', $jobId)->where('status', 0)->count();

        $progress = $total > 0 ? round(($done / $total) * 100) : 0;
        return response()->json(['progress' => $progress]);
    });

    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/', [Home::class, 'master'])->name('index');
        Route::get('/user', [Home::class, 'user'])->name('user');
        Route::get('/user/{id}/detail', [Home::class, 'userEdit'])->name('user.edit');
        Route::put('/user/{user}', [Home::class, 'userUpdate'])->name('user.update');
        Route::resource('unit', UnitController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('payment', PaymentController::class);
        Route::resource('teach', TeachController::class);
        Route::resource('student', StudentController::class);
        Route::resource('program', ProgramController::class);
        Route::resource('layanan', AddonController::class);
    });
});
