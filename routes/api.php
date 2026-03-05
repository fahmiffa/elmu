<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Home;
use Illuminate\Support\Facades\Route;

Route::post('/webhook', [Home::class, 'midtransHook']);

Route::post('/status', function () {
    return response()->json([
        'status'  => false,
        'versi'   => env('APP_VERSION'),
        'message' => 'Mohon maaf, aplikasi sedang dalam perbaikan.\nSilahkan coba lagi secara berkala',
    ], 200);
});

Route::prefix('fire')->group(function () {
    Route::post('/reg', [StudentController::class, 'reg']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/kelas', [AcademicController::class, 'kelas']);
    Route::get('/payment', [BillingController::class, 'payment']);
    Route::post('/forget', [AuthController::class, 'forget']);
    Route::post('/fcm', [ContentController::class, 'fcm']);
});

Route::middleware('jwt')->group(function () {
    Route::post('/pay', [Home::class, 'midtransPay']);
    Route::get('/data', [StudentController::class, 'data']);
    Route::get('/murid', [StudentController::class, 'murid']);
    Route::get('/materi', [AcademicController::class, 'materi']);
    Route::get('/campaign', [ContentController::class, 'campaign']);
    Route::get('/miska', [ContentController::class, 'miska']);
    Route::post('/data/{par}', [StudentController::class, 'Updata']);
    Route::get('/program', [AcademicController::class, 'program']);
    Route::get('/unit', [AcademicController::class, 'unit']);
    Route::get('/bill', [BillingController::class, 'bill']);
    Route::get('/video', [ContentController::class, 'videos']);
    Route::post('/video', [ContentController::class, 'video']);
    Route::get('/level', [AcademicController::class, 'level']);
    Route::post('/level', [AcademicController::class, 'Uplevel']);
    Route::post('/pass', [AuthController::class, 'upass']);
    Route::get('/tagihan', [BillingController::class, 'tagihan']);
    Route::post('/bill', [BillingController::class, 'billStore']);
    Route::get('/price/{kelas}/{product}', [AcademicController::class, 'price']);
    Route::get('/jadwal', [AcademicController::class, 'jadwal']);
    Route::post('/jadwal', [AcademicController::class, 'UpJadwal']);
    Route::post('/report', [ContentController::class, 'ureport']);
    Route::get('/report', [ContentController::class, 'report']);
    Route::get('/raport', [AcademicController::class, 'raport']);
});
