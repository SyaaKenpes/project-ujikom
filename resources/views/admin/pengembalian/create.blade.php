@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-3xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Detail Pengembalian Alat</h2>

            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div><span class="font-semibold text-gray-600">Peminjam:</span> {{ $peminjaman->user->name }}</div>
                <div><span class="font-semibold text-gray-600">Tanggal Pinjam:</span> {{ $peminjaman->tgl_pinjam }}</div>
                <div><span class="font-semibold text-gray-600">Batas Kembali:</span> {{ $peminjaman->tgl_kembali_plan }}</div>
                <div><span class="font-semibold text-gray-600">Tanggal Hari Ini:</span> {{ $tglSekarang->format('Y-m-d') }}</div>
            </div>

            <div class="mb-6">
                <span class="font-semibold text-gray-600 block mb-2">Alat yang Dipinjam:</span>
                <ul class="list-disc list-inside bg-gray-50 p-3 rounded">
                    @foreach ($peminjaman->detailPinjams as $detail)
                        <li>{{ $detail->alat->nama_alat }} ({{ $detail->jumlah }} pcs)</li>
                    @endforeach
                </ul>
            </div>

            @if ($telatHari > 0)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Peringatan!</strong> Telat {{ $telatHari }} hari. <br>
                    Denda Otomatis: <strong>Rp {{ number_format($dendaOtomatis, 0, ',', '.') }}</strong>
                </div>
            @else
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    Pengembalian tepat waktu. Tidak ada denda keterlambatan.
                </div>
            @endif

            <form action="{{ route('admin.pengembalian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                <!-- Dropdown Kondisi dengan Harga -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kondisi Alat Saat Dikembalikan</label>
                    <select name="kondisi_kembali" id="kondisi_kembali" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="Bagus" data-denda="0">Bagus / Lengkap (Rp 0)</option>
                        <option value="Lecet" data-denda="10000">Sedikit Rusak / Lecet (Rp 10.000)</option>
                        <option value="Rusak" data-denda="50000">Rusak (Rp 50.000)</option>
                        <option value="Hilang" data-denda="100000">Hilang (Rp 100.000)</option>
                    </select>
                </div>

                <!-- Kotak Rincian (Kalkulator Otomatis) -->
                <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Rincian Total Denda</h3>
                    
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Denda Keterlambatan ({{ $telatHari }} hari)</span>
                        <span id="teks-denda-telat" data-telat="{{ $dendaOtomatis }}">Rp {{ number_format($dendaOtomatis, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Denda Kondisi Barang (<span id="label-kondisi">Bagus</span>)</span>
                        <span id="teks-denda-kondisi">Rp 0</span>
                    </div>
                    
                    <hr class="my-3 border-gray-300">
                    
                    <div class="flex justify-between font-bold text-lg text-red-600">
                        <span>Total Denda Dibayar</span>
                        <span id="teks-total-denda">Rp {{ number_format($dendaOtomatis, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Input Hidden ini buat ngirim denda kerusakan ke Database secara diam-diam -->
                <input type="hidden" name="denda_kerusakan" id="input-denda-kerusakan" value="0">

                <div class="flex gap-4">
                    <a href="{{ route('admin.pengembalian.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg text-sm font-semibold transition text-center flex-none w-1/3 text-base pt-2.5">Batal</a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full flex-grow">
                        Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Javascript buat Kalkulator Otomatisnya -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectKondisi = document.getElementById('kondisi_kembali');
            
            const teksDendaKondisi = document.getElementById('teks-denda-kondisi');
            const teksTotalDenda = document.getElementById('teks-total-denda');
            const labelKondisi = document.getElementById('label-kondisi');
            const inputDendaKerusakan = document.getElementById('input-denda-kerusakan');
            
            // Ambil nominal denda telat dari PHP yang udah dirender
            const dendaTelat = parseInt(document.getElementById('teks-denda-telat').getAttribute('data-telat')) || 0;

            // Fungsi biar angkanya jadi format Rupiah (contoh: 50000 -> Rp 50.000)
            const formatRupiah = (angka) => {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            };

            // Pas dropdown diganti, fungsi ini jalan
            selectKondisi.addEventListener('change', function() {
                const kondisi = this.options[this.selectedIndex].value;
                const dendaKondisi = parseInt(this.options[this.selectedIndex].getAttribute('data-denda'));
                
                // Kalkulasi total
                const totalSemua = dendaTelat + dendaKondisi;

                // Update text di layar
                labelKondisi.innerText = kondisi;
                teksDendaKondisi.innerText = formatRupiah(dendaKondisi);
                teksTotalDenda.innerText = formatRupiah(totalSemua);

                // Update value di input hidden buat dikirim ke Controller
                inputDendaKerusakan.value = dendaKondisi;
            });
        });
    </script>
@endsection