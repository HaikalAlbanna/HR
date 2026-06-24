<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\KaryawanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/karyawan/report.pdf', [KaryawanController::class, 'report'])->name('dashboard.karyawan.report');
    Route::get('/dashboard/karyawan', [KaryawanController::class, 'index'])->name('dashboard.karyawan.index');
    Route::post('/dashboard/karyawan', [KaryawanController::class, 'store'])->name('dashboard.karyawan.store');
    Route::get('/dashboard/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('dashboard.karyawan.edit');
    Route::put('/dashboard/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('dashboard.karyawan.update');
    Route::delete('/dashboard/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('dashboard.karyawan.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
