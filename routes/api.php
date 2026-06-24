<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\KaryawanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/token', [AuthTokenController::class, 'store']);

Route::middleware('jwt')->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
    Route::post('/karyawan', [KaryawanController::class, 'store']);
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
});
