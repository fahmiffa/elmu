<?php
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\Home;

Route::post('/webhook', [Home::class, 'midtransHook']);

Route::prefix('fire')->group(function () {
    Route::post('/reg', [ApiController::class, 'reg']);
    Route::post('/refresh', [ApiController::class, 'refresh']);
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/kelas', [ApiController::class, 'kelas']);
});

Route::middleware('jwt')->group(function () {
    Route::post('/pay', [Home::class, 'midtransPay']);
    Route::get('/data', [ApiController::class, 'data']);
    Route::get('/program', [ApiController::class, 'program']);
    Route::get('/unit', [ApiController::class, 'unit']);
    Route::get('/bill', [ApiController::class, 'bill']);
    Route::post('/bill', [ApiController::class, 'billStore']);
    Route::get('/price/{kelas}/{product}', [ApiController::class, 'price']);
});
