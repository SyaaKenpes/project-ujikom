<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\AdminController;
use App\Http\Controllers\WEB\PetugasController;
use App\Http\Controllers\WEB\PeminjamanController;
use App\Http\Controllers\WEB\AuthController;


Route::get('/', function () {
    return view('login');
});

//admin
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Alat
    Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
    Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');

    // CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
});

//petugas
Route::middleware(['auth', 'role.petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Peminjaman & Persetujuan
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman', [PetugasController::class, 'storePeminjaman'])->name('peminjaman.store');

    // Pengembalian & Denda
    Route::post('/pengembalian', [PetugasController::class, 'storePengembalian'])->name('pengembalian.store');
});

//peminjam
Route::middleware(['auth', 'role.peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    // Katalog & Pengajuan
    Route::get('/katalog', [PeminjamController::class, 'indexKatalog'])->name('katalog.index');
    Route::post('/peminjaman', [PeminjamController::class, 'storePengajuan'])->name('peminjaman.store');
    Route::get('/riwayat', [PeminjamController::class, 'indexRiwayat'])->name('riwayat.index');
});

// Route Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


