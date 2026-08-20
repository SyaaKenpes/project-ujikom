@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Halaman -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Manajemen Transaksi Peminjaman / Proses Pengembalian</h2>
    </div>

    <!-- Card Container (Style sama persis dengan gambar) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        
        <!-- Header Card -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-md font-bold text-gray-700">Form Pengembalian Barang</h3>
        </div>

        <!-- Body Form -->
        <div class="p-6">
            <form action="{{ route('admin.pengembalian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                <!-- Kotak Info Data Peminjaman -->
                <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-md border border-green-200">
                    <p class="text-sm mb-1"><span class="font-bold uppercase tracking-wider text-xs">Nama Peminjam:</span> {{ $peminjaman->user->name }}</p>
                    <p class="text-sm mb-2"><span class="font-bold uppercase tracking-wider text-xs">Rencana Kembali:</span> {{ $peminjaman->tgl_kembali_plan }}</p>
                    <p class="font-bold uppercase tracking-wider text-xs mt-3 mb-1">Alat yang dipinjam:</p>
                    <ul class="list-disc ml-5 text-sm">
                        @foreach($peminjaman->detailPinjams as $detail)
                            <li>{{ $detail->alat->nama_alat ?? 'Nama Alat Kosong' }} ({{ $detail->jumlah }} pcs)</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Input Kondisi -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kondisi Barang Saat Dikembalikan</label>
                    <select name="kondisi_kembali" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>-- Pilih Kondisi --</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                </div>

                <!-- Input Denda Kerusakan -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Denda Kerusakan / Kehilangan</label>
                    <input type="number" name="denda_kerusakan" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 50000 (Kosongkan jika barang aman)">
                    <p class="text-xs text-gray-400 mt-1">*Catatan: Denda keterlambatan akan dihitung otomatis oleh sistem sebesar Rp 2.000/hari.</p>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-3 mt-8">
                    <a href="{{ route('admin.peminjaman.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded text-sm transition duration-150 ease-in-out">
                        Batal
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm transition duration-150 ease-in-out">
                        Proses Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection