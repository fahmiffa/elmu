<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\Home;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeachController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VidoesController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;


// Route::any('{any}', function () {
//    abort(404, 'Data karyawan tidak ditemukan');
// });


Route::get('/kebijakan-privasi', function () {
    return view('policy');
});

Route::get('/payment/{par}', function ($par) {
    return view('payment', compact('par'));
});

Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    Artisan::call('db:seed');
    File::put(storage_path('logs/laravel.log'), '');
    return 'Log cleared';
});

Route::get('/video/{id}', [AuthController::class, 'video']);
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'loginForm']);
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('/chart-json/{par}', [Home::class, 'chart'])->name('chart.sales');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [Home::class, 'index'])->name('home');
    Route::get('/akademik', [Home::class, 'akademik'])->name('akademik');
    Route::get('/laporan-unit', [Home::class, 'reportUnit'])->middleware('isRole')->name('report.unit');
    Route::get('/fierbase', [Home::class, 'fcm'])->name('fcm');
    Route::middleware('restrictOperator')->group(function () {
        Route::get('/pendaftaran', [Home::class, 'reg'])->name('reg.index');
        Route::post('/pendaftaran', [Home::class, 'regStore'])->name('reg.store');
        Route::get('/pendaftaran/tambah', [Home::class, 'AddReg'])->name('reg.create');
        Route::get('/pendaftaran/{id}/edit', [Home::class, 'regEdit'])->name('reg.edit');
        Route::put('/pendaftaran/{id}', [Home::class, 'regUpdate'])->name('reg.update');
        Route::post('/pendaftaran/{id}/hapus', [Home::class, 'regDestroy'])->name('reg.destroy');
        Route::resource('report', ReportController::class);
        Route::resource('raport', RaportController::class);
        Route::resource('campaign', CampaignController::class);
        Route::post('/jadwal/{id}/hapus', [ScheduleController::class, 'hapus'])->name('hapus');
        Route::resource('jadwal', ScheduleController::class);
    });
    Route::get('/level', [Home::class, 'level'])->name('level');
    Route::prefix('pembayaran')->name('pay.')->group(function () {
        Route::get('/bulanan', [Home::class, 'monthly'])->name('monthly');
        Route::get('/layanan', [Home::class, 'service'])->name('service');
    });
    Route::post('/pembayaran/{id}/{par}', [Home::class, 'payment'])->name('payment');
    Route::post('/send/{id}/{par}', [Home::class, 'send'])->name('send');
    Route::get('setting', [Home::class, 'setting'])->name('setting');
    Route::post('/pass', [Home::class, 'pass'])->name('pass');
    Route::post('/bill', [Home::class, 'bill'])->name('bill');
    Route::post('/layanan/{id}', [Home::class, 'layanan'])->name('layanan');
    Route::post('/status/{id}', [Home::class, 'status'])->name('status');
    Route::get('/invoice/{id}', [Home::class, 'invoice'])->name('invoice');
    Route::get('/absensi', [Home::class, 'absensi'])->name('absensi');
    Route::resource('video', VidoesController::class);

    Route::get('/job-progress/{jobId}', function ($jobId) {
        $total = DB::table('head')->count();
        $done  = DB::table('paids')->where('bulan', $jobId)->where('status', 0)->count();

        $progress = $total > 0 ? round(($done / $total) * 100) : 0;
        return response()->json(['progress' => $progress]);
    });

    Route::prefix('master')->middleware('isRole')->name('master.')->group(function () {
        Route::resource('materi', MateriController::class);
        Route::get('/', [Home::class, 'master'])->name('index');
        Route::get('/user', [Home::class, 'user'])->name('user');
        Route::get('/user/create', [Home::class, 'userCreate'])->name('user.create');
        Route::post('/user', [Home::class, 'userStore'])->name('user.store');
        Route::get('/user/{id}/edit', [Home::class, 'userEditAction'])->name('user.edit-form');
        Route::put('/user/{user}/update', [Home::class, 'userUpdateData'])->name('user.update-data');
        Route::get('/user/{id}/detail', [Home::class, 'userEdit'])->name('user.edit');
        Route::get('/user/{id}/password', [Home::class, 'userPass'])->name('user.pass');
        Route::put('/user/{id}/password', [Home::class, 'userPassUpdate'])->name('user.pass.update');
        Route::put('/user/{user}', [Home::class, 'userUpdate'])->name('user.update');
        Route::resource('unit', UnitController::class);
        Route::resource('zone', ZoneController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('payment', PaymentController::class);
        Route::resource('teach', TeachController::class);
        Route::resource('student', StudentController::class);
        Route::resource('program', ProgramController::class);
        Route::resource('layanan', AddonController::class);
        Route::get('stater-kit', [AddonController::class, 'kit'])->name('kit.index');
        Route::get('stater-kit/create', [AddonController::class, 'kit'])->name('kit.create');
        Route::post('stater-kit', [AddonController::class, 'kit'])->name('kit.store');
        Route::resource('grade', GradeController::class);
        Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('log.index');
        Route::delete('/activity-log/{id}', [\App\Http\Controllers\ActivityLogController::class, 'destroy'])->name('log.destroy');
        Route::post('/activity-log/clear', [\App\Http\Controllers\ActivityLogController::class, 'clear'])->name('log.clear');
        Route::get('/unit-jadwal', [UnitController::class, 'jadwal'])->name('jadwal.index');
        Route::get('/unit-jadwal/create', [UnitController::class, 'jadwalCreate'])->name('jadwal.create');
        Route::get('/unit-jadwal/{id}/edit', [UnitController::class, 'jadwalEdit'])->name('jadwal.edit');
        Route::put('/unit-jadwal/{jadwal}', [UnitController::class, 'jadwalUpdate'])->name('jadwal.update');
        Route::post('/unit-jadwal', [UnitController::class, 'jadwalStore'])->name('jadwal.store');
        Route::post('/unit-jadwal/{id}/hapus', [UnitController::class, 'jadwalDestroy'])->name('jadwal.destroy');
    });
});
