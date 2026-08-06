<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;


class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // CRUD Alat: Menampilkan daftar alat
    public function indexAlat()
    {
        $alats = Alat::with('kategori')->get();
        return view('admin.alat.index', compact('alats'));
    }
    // Menyimpan Alat Baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'kategori_id'    => 'required',
            'nama_alat'      => 'required|string|max:255',
            'stok'           => 'required|integer',
            'status_kondisi' => 'required|string',
    ]);

    Alat::create($validatedData);

    // Catat Log Aktivitas
    LogAktivitas::create([
        'user_id'   => auth()->id(),
        'aktivitas' => 'Menambahkan alat baru: ' . $validatedData['nama_alat']
    ]);

    return redirect()->back()->with('success', 'Alat berhasil ditambahkan.');
}

    // CRUD User (Manajemen User Admin, Petugas, Peminjam)
    public function indexUser()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }
}
