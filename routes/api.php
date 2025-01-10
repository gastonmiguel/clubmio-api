<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::ApiResource('partners', PartnerController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::post('/login', [AuthController::class, 'login']);
