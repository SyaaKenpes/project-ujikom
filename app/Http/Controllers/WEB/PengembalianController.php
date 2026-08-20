<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon; // hitung hari
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas'])
                            ->latest()
                            ->get();

        return view('admin.pengembalian.index', compact('pengembalians'));
    }

    public function create($id)
    {
        // Tarik data peminjaman beserta relasinya
        $peminjaman = Peminjaman::with('user', 'detailPinjams.alat')->findOrFail($id);
        return view('admin.pengembalian.create', compact('peminjaman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_kembali' => 'required|string',
            'denda_kerusakan' => 'nullable|numeric' // Opsi input manual kalau barang rusak
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
            
            // 1. Logika Hitung Telat & Denda Keterlambatan
            $tglRencana = Carbon::parse($peminjaman->tgl_kembali_plan);
            $tglSekarang = Carbon::now();
            
            $dendaTelat = 0;
            // Kalau tanggal sekarang ngelewatin tanggal rencana, berarti telat
            if ($tglSekarang->greaterThan($tglRencana)) {
                $telatHari = $tglRencana->diffInDays($tglSekarang);
                $dendaTelat = $telatHari * 2000;
            }

            // Total denda = denda telat otomatis + denda rusak dari form
            $dendaKerusakan = $request->denda_kerusakan ?? 0;
            $totalDenda = $dendaTelat + $dendaKerusakan;

            // 2. Simpan Data Pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => $tglSekarang->toDateString(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
                'petugas_id' => Auth::id(), // ID Admin/Guru yang lagi login
            ]);

            // 3. Update Status Peminjaman & Balikin Stok
            $peminjaman->update(['status' => 'dikembalikan']);
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Barang berhasil dikembalikan. Total Denda: Rp ' . number_format($totalDenda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}