<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Home;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeachController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ZoneController;
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
    Route::get('/video', [AuthController::class, 'video']);
});

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('/chart-json/{par}', [Home::class, 'chart'])->name('chart.sales');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [Home::class, 'index'])->name('home');
    Route::get('/fierbase', [Home::class, 'fcm'])->name('fcm');
    Route::get('/pendaftaran', [Home::class, 'reg'])->name('reg');
    Route::Post('/pendaftaran', [Home::class, 'regStore'])->name('reg.store');
    Route::get('/pendaftaran/tambah', [Home::class, 'AddReg'])->name('reg.create');
    Route::get('/pembayaran', [Home::class, 'pay'])->name('pay');
    Route::post('/pembayaran/{id}/{par}', [Home::class, 'payment'])->name('payment');
    Route::post('/send/{id}/{par}', [Home::class, 'send'])->name('send');
    Route::get('/absensi', [Home::class, 'absensi'])->name('absensi');
    Route::get('/level', [Home::class, 'level'])->name('level');
    Route::get('setting', [Home::class, 'setting'])->name('setting');
    Route::post('/pass', [Home::class, 'pass'])->name('pass');
    Route::post('/bill', [Home::class, 'bill'])->name('bill');
    Route::post('/layanan/{id}', [Home::class, 'layanan'])->name('layanan');
    Route::get('/invoice/{id}', [Home::class, 'invoice'])->name('invoice');
    Route::post('/jadwal/{id}/hapus', [ScheduleController::class, 'hapus'])->name('hapus');
    Route::resource('jadwal', ScheduleController::class);
    Route::resource('report', ReportController::class);

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
        Route::resource('zone', ZoneController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('payment', PaymentController::class);
        Route::resource('teach', TeachController::class);
        Route::resource('student', StudentController::class);
        Route::resource('program', ProgramController::class);
        Route::resource('layanan', AddonController::class);
        Route::resource('grade', GradeController::class);
        Route::get('/unit-jadwal', [UnitController::class, 'jadwal'])->name('jadwal.index');
        Route::get('/unit-jadwal/create', [UnitController::class, 'jadwalCreate'])->name('jadwal.create');
        Route::get('/unit-jadwal/{id}/edit', [UnitController::class, 'jadwalEdit'])->name('jadwal.edit');
        Route::put('/unit-jadwal/{jadwal}', [UnitController::class, 'jadwalUpdate'])->name('jadwal.update');
        Route::post('/unit-jadwal', [UnitController::class, 'jadwalStore'])->name('jadwal.store');
        Route::post('/unit-jadwal/{id}/hapus', [UnitController::class, 'jadwalDestroy'])->name('jadwal.destroy');
    });
});
