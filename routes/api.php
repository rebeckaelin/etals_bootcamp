<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChirpController;

// Route::get('/test', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::get('/test', function () {
//     // dd('Route reached');
//     return response()->json(['message' => 'API test is working']);
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chirps', [ChirpController::class, 'apiIndex']);
    Route::post('/chirps', [ChirpController::class, 'apiStore']);
    Route::get('/chirps/{chirp}', [ChirpController::class, 'apiShow']);
    Route::put('/chirps/{chirp}', [ChirpController::class, 'apiUpdate']);
    Route::delete('/chirps/{chirp}', [ChirpController::class, 'apiDestroy']);
});