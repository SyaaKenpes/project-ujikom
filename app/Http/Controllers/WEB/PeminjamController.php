<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // 1. Katalog Alat
    public function indexKatalog(Request $request)
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();

        $kategoris = Kategori::all();

        $query = Alat::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        // Eksekusi query
        $alats = $query->get();

        return view('peminjam.katalog', compact('alats', 'kategoris'));
    }

    // 2. Submit Pengajuan Peminjaman
public function storePengajuan(Request $request)
{
    // 1. Tambahin pesan peringatan custom di parameter kedua validate()
    $request->validate([
        'tgl_kembali_plan' => 'required|date|after_or_equal:today',
        'alat_id'          => 'required|array|min:1', 
        'jumlah'           => 'array',
    ], [
        // Daftar pesan error yang bakal muncul di layar
        'tgl_kembali_plan.required'       => 'Tanggal rencana pengembalian wajib diisi!',
        'tgl_kembali_plan.after_or_equal' => 'Tanggal pengembalian tidak boleh lewat dari hari ini.',
        'alat_id.required'                => 'Pilih minimal 1 alat yang ingin dipinjam (centang kotaknya)!',
        'alat_id.min'                     => 'Pilih minimal 1 alat yang ingin dipinjam (centang kotaknya)!',
    ]);

    DB::beginTransaction();
    try {
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => $request->tgl_kembali_plan,
            'status' => 'diajukan',
        ]);

        foreach ($request->alat_id as $index => $alatId) {
            if (isset($request->jumlah[$index]) && $request->jumlah[$index] > 0) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }
        }

        DB::commit();
        return redirect()->route('peminjam.riwayat.index')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
    }
}

    // 3. Riwayat Peminjaman
    public function indexRiwayat()
    {
        $peminjamans = Peminjaman::with('detailPinjams.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }
}