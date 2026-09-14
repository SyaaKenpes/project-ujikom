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
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
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
    // Validasi input dari form
    $request->validate([
        'kondisi_kembali' => 'required|string',
        'denda' => 'nullable' 
    ]);

    DB::beginTransaction();
    try {
        // 1. Ambil data peminjaman beserta relasi detail alatnya
        $peminjaman = \App\Models\Peminjaman::with('detailPinjams')->findOrFail($id);

        // Bersihkan format denda (misal dari "120.000" jadi 120000)
        $denda = 0;
        if ($request->denda) {
            $denda = str_replace('.', '', $request->denda);
        }

        // 2. Update status di tabel peminjaman
        $peminjaman->update([
            'status' => 'Dikembalikan'
        ]);

        // 3. Simpan data ke tabel pengembalian
        \App\Models\Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $denda,
            'petugas_id' => auth()->id(),
        ]);

        // 4. Tambahkan kembali stok alat sesuai jumlah yang dipinjam
        foreach ($peminjaman->detailPinjams as $detail) {
            $alat = \App\Models\Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
        }

        DB::commit();
        return redirect()->route('petugas.pengembalian.index')->with('success', 'Pengembalian barang berhasil diproses dan stok alat bertambah!');

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