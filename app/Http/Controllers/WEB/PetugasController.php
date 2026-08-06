<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])->latest()->get();
        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    public function setujui(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Logika persetujuan peminjaman
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

        // Simpan data pengembalian
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $request->denda ?? 0,
            'petugas_id' => auth()->id(),
        ]);

        // Update status peminjaman jadi selesai
        $peminjaman->update(['status' => 'selesai']);

        // Kembalikan stok alat ke inventaris
        foreach ($peminjaman->detailPinjams as $detail) {
            $alat = Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
        }

        DB::commit();
        return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

}

