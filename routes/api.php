<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetDatabaseConnection;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', SetDatabaseConnection::class])->group(function () {

    Route::get('/partners/pages', [PartnerController::class, 'getTotalPages']);
    Route::apiResource('partners', PartnerController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});
