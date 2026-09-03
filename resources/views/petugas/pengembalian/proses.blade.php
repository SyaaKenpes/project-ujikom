@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="mb-4">
        <h1 class="text-xl font-bold text-gray-800 mt-2">Form Proses Pengembalian Alat</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ringkasan Peminjaman -->
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
            <h2 class="font-semibold text-gray-700 mb-3 border-b pb-2">Detail Peminjaman</h2>
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong class="text-gray-800">Peminjam:</strong> {{ $peminjaman->user->name ?? '-' }}</p>
                <p><strong class="text-gray-800">Tgl Pinjam:</strong> {{ $peminjaman->tgl_pinjam }}</p>
                <p><strong class="text-gray-800">Rencana Kembali:</strong> {{ $peminjaman->tgl_kembali_plan }}</p>
                
                <div class="mt-4">
                    <strong class="text-gray-800 block mb-1">Daftar Alat:</strong>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($peminjaman->detailPinjams as $detail)
                            <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Pengembalian -->
        <div class="md:col-span-2 bg-white p-5 rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Alat Saat Dikembalikan</label>
                    <select name="kondisi_kembali" class="w-full text-sm border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Denda (Rp)</label>
                    <input type="text" name="denda" value="0" placeholder="0" class="w-full text-sm border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-xs text-gray-500 mt-1 block">*Isi 0 jika tidak ada denda.</span>
                </div>

                <div class="pt-4 border-t flex justify-end gap-2">
                    <a href="{{ route('petugas.pengembalian.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection