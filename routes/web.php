<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\BukuController as ApiBukuController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('register');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Buku (Web & API Modul 4)
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/{id}', [BukuController::class, 'show'])->name('buku.show');

    Route::get('/api/buku', [BukuController::class, 'index'])->name('api.buku.index');
    Route::post('/api/buku', [BukuController::class, 'store'])->name('api.buku.store');
    Route::put('/api/buku/{id}', [BukuController::class, 'update'])->name('api.buku.update');
    Route::delete('/api/buku/{id}', [BukuController::class, 'destroy'])->name('api.buku.destroy');


    // KHUSUS SISWA
    Route::middleware(['role:Siswa'])->group(function () {
        Route::get('/riwayat-saya', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');
        Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });

   // KHUSUS PETUGAS & ADMIN
    Route::middleware(['role:Petugas|Admin'])->group(function () {
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::patch('/peminjaman/{id}/kembali', [PeminjamanController::class, 'updateStatus'])->name('peminjaman.updateStatus');
        Route::get('/laporan', [PeminjamanController::class, 'laporan'])->name('laporan.index');
    });

    // KHUSUS ADMIN
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', AdminUserController::class);
    });

    Route::get('/buku-cari', [BukuController::class, 'cariBuku'])->middleware('auth');

    Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])->middleware('auth');

    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->middleware('auth');
});

require __DIR__.'/auth.php';