<?php
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Home;
use Illuminate\Support\Facades\Route;

Route::post('/webhook', [Home::class, 'midtransHook']);

Route::prefix('fire')->group(function () {
    Route::post('/reg', [ApiController::class, 'reg']);
    Route::post('/refresh', [ApiController::class, 'refresh']);
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/kelas', [ApiController::class, 'kelas']);
    Route::get('/payment', [ApiController::class, 'payment']);
    Route::post('/forget', [ApiController::class, 'forget']);
    Route::post('/fcm', [ApiController::class, 'fcm']);
});

Route::middleware('jwt')->group(function () {
    Route::post('/pay', [Home::class, 'midtransPay']);
    Route::get('/data', [ApiController::class, 'data']);
    Route::post('/data/{par}', [ApiController::class, 'Updata']);
    Route::get('/program', [ApiController::class, 'program']);
    Route::get('/unit', [ApiController::class, 'unit']);
    Route::get('/bill', [ApiController::class, 'bill']);
    Route::get('/video', [ApiController::class, 'videos']);
    Route::get('/level', [ApiController::class, 'level']);
    Route::post('/level', [ApiController::class, 'Uplevel']);
    Route::post('/pass', [ApiController::class, 'upass']);
    Route::get('/tagihan', [ApiController::class, 'tagihan']);
    Route::post('/bill', [ApiController::class, 'billStore']);
    Route::get('/price/{kelas}/{product}', [ApiController::class, 'price']);
    Route::get('/jadwal', [ApiController::class, 'jadwal']);
    Route::post('/jadwal', [ApiController::class, 'UpJadwal']);
    Route::post('/report', [ApiController::class, 'ureport']);
    Route::get('/report', [ApiController::class, 'report']);
    Route::get('/raport', [ApiController::class, 'raport']);
});
