<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Public Routes (Tidak perlu token) 
Route::post('/register', [AuthController::class, 'register']); Route::post('/login', [AuthController::class, 'login']); 

// Protected Routes (Wajib membawa Bearer Token dari Sanctum) 
Route::middleware('auth:sanctum')->group(function () {          
    Route::get('/me', [AuthController::class, 'me']);     Route::post('/logout', [AuthController::class, 'logout']);


    Route::middleware('role.admin')->group(function () {         
        // Route untuk hak akses admin     
    });     
    Route::middleware('role.petugas')->group(function () {         
        // Route untuk hak akses petugas     
    });     
    Route::middleware('role.peminjam')->group(function () {         
        // Route untuk hak akses peminjam     
    }); 
});
