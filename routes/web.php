<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WEB\AdminController;
use App\Http\Controllers\WEB\PetugasController;
use App\Http\Controllers\WEB\PeminjamController; 
use App\Http\Controllers\WEB\AuthController;

Route::get('/', function () {
    return view('login');
});

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Alat
    Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
    Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
    Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
    Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
    Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
    Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

    // CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');

    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    // CRUD Peminjaman
    Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
    Route::get('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
    Route::get('/peminjaman/{$id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.UpdateStatus');
    Route::get('/peminjaman/{$id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.index');

}); 

// ==========================================
// PETUGAS ROUTES
// ==========================================
Route::middleware(['auth', 'role.petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Peminjaman & Persetujuan
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman', [PetugasController::class, 'storePeminjaman'])->name('peminjaman.store');

    // Pengembalian & Denda
    Route::post('/pengembalian', [PetugasController::class, 'storePengembalian'])->name('pengembalian.store');
});

// ==========================================
// PEMINJAM ROUTES
// ==========================================
Route::middleware(['auth', 'role.peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    // Katalog & Pengajuan
    Route::get('/katalog', [PeminjamController::class, 'indexKatalog'])->name('katalog.index');
    Route::post('/peminjaman', [PeminjamController::class, 'storePengajuan'])->name('peminjaman.store');
    Route::get('/riwayat', [PeminjamController::class, 'indexRiwayat'])->name('riwayat.index');
});

// ==========================================
// AUTH ROUTES
// ==========================================
// Route Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


