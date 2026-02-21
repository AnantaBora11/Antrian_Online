<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PublicController;

// Public
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/api/antrian', [PublicController::class, 'apiAntrian'])->name('api.antrian');
Route::post('/api/antrian/ambil', [PublicController::class, 'ambilAntrian'])->name('api.antrian.ambil');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin (protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/antrian', [DashboardController::class, 'listAntrian'])->name('antrian');
    Route::post('/panggil/{layanan}', [DashboardController::class, 'panggilBerikutnya'])->name('panggil');
    Route::post('/panggil-prev/{layanan}', [DashboardController::class, 'panggilSebelumnya'])->name('panggil.prev');
    Route::post('/selesai/{antrian}', [DashboardController::class, 'selesaikan'])->name('selesai');
    Route::post('/batal/{antrian}', [DashboardController::class, 'batalkan'])->name('batal');
});
