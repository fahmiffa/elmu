<?php
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('fire')->group(function () {
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/logout', [ApiController::class, 'logout']);
});

Route::middleware('jwt')->group(function () {
    Route::get('/items', [ApiController::class, 'Items']);
});
