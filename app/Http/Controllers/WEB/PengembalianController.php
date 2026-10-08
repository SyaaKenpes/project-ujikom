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

        $tglRencana = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
        $tglSekarang = \Carbon\Carbon::now()->startOfDay();

        $telatHari = 0;
        $dendaOtomatis = 0;

            if ($tglSekarang->greaterThan($tglRencana)) {
        $telatHari = (int) $tglRencana->diffInDays($tglSekarang);
        $dendaOtomatis = $telatHari * 2000; // Sesuaikan tarif denda per hari
    }

    return view('admin.pengembalian.create', compact('peminjaman', 'telatHari', 'dendaOtomatis', 'tglSekarang'));
    }

    public function prosesPengembalian(Request $request, $id)
    {
        // 1. Validasi input (kondisi sekarang bentuknya array per-barang)
        $request->validate([
            'kondisi' => 'required|array',
            'denda' => 'nullable|numeric', // Denda total sudah dihitung JS di frontend
            'denda_tambahan' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            // Ambil data peminjaman berdasarkan ID dari URL
            $peminjaman = Peminjaman::with(['detailPinjams.alat', 'user'])->findOrFail($id);
            
            // Ambil total denda dari hidden input
            $denda = $request->denda ?? 0;

            // 2. Update status peminjaman
            $peminjaman->update(['status' => 'dikembalikan']);

            $rekapKondisi = [];

            // 3. Looping untuk urus stok dan catat kondisi masing-masing barang
            foreach ($peminjaman->detailPinjams as $detail) {
    
    
    if ($detail->status === 'disetujui') {
        
        $statusKondisiItem = $request->kondisi[$detail->id] ?? 'Bagus'; 
        
        
        $rekapKondisi[] = ($detail->alat->nama_alat ?? 'Alat') . ' (' . $statusKondisiItem . ')';

        // LOGIKA STOK: Tambahkan kembali stok HANYA JIKA barang tidak "Hilang"
        if ($statusKondisiItem !== 'Hilang' && $statusKondisiItem !== 'hilang') {
            $alat = \App\Models\Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
        }
        }
    }

            // Gabungkan array menjadi satu string, dipisah koma
            $stringKondisiKembali = implode(', ', $rekapKondisi);

            // 4. Simpan ke tabel Pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => Carbon::now()->toDateString(),
                'kondisi_kembali' => $stringKondisiKembali,
                'denda' => $denda,
                'petugas_id' => Auth::id(),
            ]);

            // 5. Catat Log Aktivitas (Gua rapihin kodingan lu yang sebelumnya nyatet 2x jadi 1x aja)
            $namaUser = $peminjaman->user->name ?? 'User';
            \App\Models\LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Memproses pengembalian alat atas nama ' . $namaUser . ($denda > 0 ? ' dengan total denda Rp ' . number_format($denda, 0, ',', '.') : '.')
            ]);

            DB::commit();
            return redirect()->route('admin.pengembalian.index')->with('success', 'Barang berhasil dikembalikan. Total Denda: Rp ' . number_format($denda, 0, ',', '.'));

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

}