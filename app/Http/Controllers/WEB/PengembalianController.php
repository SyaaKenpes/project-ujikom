<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon; // hitung hari
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap inputan dari kolom search
        $search = $request->input('search');

        // 2. Ambil data yang statusnya 'dipinjam' dan filter kalau ada pencarian
        $sedangDipinjam = Peminjaman::with('user', 'detailPinjams.alat')
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                // Cari berdasarkan nama user di tabel peminjaman
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // 3. Ambil data riwayat dan filter juga kalau ada pencarian
        $riwayatKembali = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($search, function ($query, $search) {
                // Karena relasinya lebih dalam (Pengembalian -> Peminjaman -> User), pakai dot notation
                return $query->whereHas('peminjaman.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();
                
        // 4. Lempar variabel $search ke view biar form Blade-nya tetep nyimpen teks yang diketik
        return view('admin.pengembalian.index', compact('sedangDipinjam', 'riwayatKembali', 'search'));
    }

    public function create($id)
    {
        $peminjaman = Peminjaman::with('user', 'detailPinjams.alat')->findOrFail($id);
    
        // Hitung telat dan denda otomatis
        $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
        $tglSekarang = \Carbon\Carbon::now();
    
        $telatHari = 0;
        $dendaOtomatis = 0;
    
        if ($tglSekarang->greaterThan($tglRencana)) {
            $telatHari = $tglRencana->diffInDays($tglSekarang); 
            $dendaOtomatis = $telatHari * 2000; // Contoh: Denda Rp 2.000 per hari telat
        }

    return view('admin.pengembalian.create', compact('peminjaman', 'telatHari', 'dendaOtomatis', 'tglSekarang'));
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

            // 4. Catat aktivitas ke tabel log sistem
            \App\Models\LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Memproses pengembalian alat atas nama ' . $peminjaman->user->name . ($totalDenda > 0 ? ' dan mengenakan denda Rp ' . number_format($totalDenda, 0, ',', '.') : '.')
            ]);

            DB::commit();
            \App\Models\LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Memproses pengembalian alat untuk peminjaman ID #' . $peminjaman->id . ($totalDenda > 0 ? ' dan mengenakan denda Rp ' . number_format($totalDenda, 0, ',', '.') : '.')
            ]);
            return redirect()->route('admin.peminjaman.index')->with('success', 'Barang berhasil dikembalikan. Total Denda: Rp ' . number_format($totalDenda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // 1. Fungsi buat nampilin halaman History + Filter + Pagination
    public function history(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Tarik data relasinya dari awal
        $query = \App\Models\Pengembalian::with(['peminjaman.user', 'petugas'])->latest();

        // Kalau admin ngisi rentang tanggal, filter datanya!
        if ($startDate && $endDate) {
            $query->whereBetween('tgl_kembali', [$startDate, $endDate]);
        }

        // Panggil pagination, maksimal 10 data per halaman
        $histories = $query->paginate(10);

        // Biar pas pindah halaman (pagination) filternya gak kereset
        $histories->appends($request->all());

        return view('admin.history', compact('histories', 'startDate', 'endDate'));
    }

    // 2. Modifikasi fungsi cetak PDF biar ngikutin Filter Tanggal
    public function cetakLaporan(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = \App\Models\Pengembalian::with(['peminjaman.user', 'petugas'])->latest();

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_kembali', [$startDate, $endDate]);
        }

        // Kalau cetak PDF gak usah di-paginate, get() semuanya sesuai filter
        $riwayatKembali = $query->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan_pdf', compact('riwayatKembali'));
        return $pdf->download('laporan-peminjaman.pdf');
    }
}