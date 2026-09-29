<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    // Menyetujui Peminjaman (Partial Approval + Anti Race Condition)
    public function setujuiPeminjaman(Request $request, $id)
    {
        // Validasi array barang yang dicentang dari modal persetujuan
        $request->validate([
            'approved_items' => 'nullable|array',
            'approved_items.*' => 'exists:detail_pinjam,id'
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams.alat', 'user')->findOrFail($id);
            $approvedItems = $request->approved_items ?? [];

            // SKENARIO 1: Petugas uncheck semua barang (Otomatis Ditolak)
            if (empty($approvedItems)) {
                $peminjaman->update(['status' => 'ditolak']);

                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->update(['status' => 'dibatalkan_stok_habis']);
                }

                \App\Models\LogAktivitas::create([
                    'user_id' => auth()->id(),
                    'aktivitas' => 'Petugas menolak peminjaman ID #' . $peminjaman->id . ' (semua barang dibatalkan).'
                ]);

                DB::commit();
                return redirect()->back()->with('error', 'Peminjaman ditolak karena tidak ada barang yang disetujui.');
            }

            // SKENARIO 2: Ada barang yang dicentang/disetujui
            foreach ($peminjaman->detailPinjams as $detail) {
                
                // Cek apakah ID detail ini ada di list centang petugas
                if (in_array($detail->id, $approvedItems)) {
                    
                    // Gembok data alat biar gak kena race condition
                    $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();

                    // Cek stok real-time
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi (Sisa: {$alat->stok}).");
                    }

                    // Kurangi stok & tandai disetujui
                    $alat->decrement('stok', $detail->jumlah);
                    $detail->update(['status' => 'disetujui']);

                } else {
                    // Kalau gak dicentang, tandai dibatalkan karena stok habis/kosong
                    $detail->update(['status' => 'dibatalkan_stok_habis']);
                }
            }

            // Ubah status transaksi utama jadi dipinjam
            $peminjaman->update(['status' => 'dipinjam']);

            // Catat log aktivitas petugas
            \App\Models\LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Petugas menyetujui peminjaman ID #' . $peminjaman->id . ' atas nama ' . ($peminjaman->user->name ?? 'User')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman berhasil diproses dan stok telah diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan daftar peminjaman yang sedang aktif (dipinjam atau telat) untuk dimonitor pengembaliannya
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('peminjamans', 'search'));
    }

    public function laporan(Request $request)
    {
        $status = $request->input('status');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            // KUNCI UTAMA: Hanya ambil data yang statusnya sudah Dikembalikan
            ->where('status', 'Dikembalikan')
            
            // Filter tanggal jika diisi
            ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
                return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
    }

    // Menampilkan halaman khusus cetak (print preview)
    public function cetakLaporan(Request $request)
    {
        $status = $request->input('status');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
                return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.cetak', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
    }

public function prosesPengembalian(Request $request, $id)
{
    // 1. Validasi input dari form baru
    $request->validate([
        'kondisi' => 'required|array', 
        'denda' => 'nullable|numeric', 
        'denda_tambahan' => 'nullable|numeric'
    ]);

    DB::beginTransaction();
    try {
        // 2. Ambil data peminjaman beserta relasi detail alatnya
        $peminjaman = \App\Models\Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        // Ambil total denda (sudah digabung semua oleh JavaScript)
        $denda = $request->denda ?? 0;

        // 3. Update status di tabel peminjaman
        $peminjaman->update([
            'status' => 'Dikembalikan'
        ]);

        $rekapKondisi = []; // Array buat nampung teks "Nama Alat (Kondisinya)"

        // 4. Looping per-barang untuk urus stok dan catat kondisi masing-masing
        foreach ($peminjaman->detailPinjams as $detail) {
    
    // CUMA PROSES BARANG YANG STATUSNYA DISETUJUI / DI-ACC
    if ($detail->status === 'disetujui') {
        
        $statusKondisiItem = $request->kondisi[$detail->id] ?? 'Bagus'; 
        
        // Bikin rekap teks buat disimpen ke tabel Pengembalian
        // Hasilnya misal: "Router Mikrotik (Bagus)"
        $rekapKondisi[] = ($detail->alat->nama_alat ?? 'Alat') . ' (' . $statusKondisiItem . ')';

        // LOGIKA STOK: Tambahkan kembali stok HANYA JIKA barang tidak "Hilang"
        if ($statusKondisiItem !== 'Hilang' && $statusKondisiItem !== 'hilang') {
            $alat = \App\Models\Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
            }
        }
    }

        // Gabung array rekap jadi satu string dipisah koma
        $stringKondisiKembali = implode(', ', $rekapKondisi);

        // 5. Simpan data ke tabel pengembalian
        \App\Models\Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $stringKondisiKembali, // Disimpen sbg: "Alat A (Bagus), Alat B (Rusak)"
            'denda' => $denda,
            'petugas_id' => auth()->id(),
        ]);

        DB::commit();
        return redirect()->route('petugas.pengembalian.index')->with('success', 'Pengembalian barang berhasil diproses!');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function formPengembalian($id)
    {
        // Ambil data peminjaman beserta relasi user dan detail alatnya
        $peminjaman = \App\Models\Peminjaman::with(['user', 'detailPinjams.alat'])->findOrFail($id);
        
        // LOGIKA DENDA
        $tglSekarang = \Carbon\Carbon::now()->startOfDay();
        $batasKembali = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay(); 
        
        $telatHari = 0;
        $dendaOtomatis = 0;

        // Cek apakah tanggal sekarang melebih batas tanggal kembali
        if ($tglSekarang->greaterThan($batasKembali)) {
            $telatHari = (int) $batasKembali->diffInDays($tglSekarang);
            
            // Denda Rp 2.000 per hari (disamakan dengan Admin)
            $dendaOtomatis = $telatHari * 2000; 
        }
        
        return view('petugas.pengembalian.proses', compact(
            'peminjaman', 
            'tglSekarang', 
            'telatHari', 
            'dendaOtomatis'
        ));
    }   
}