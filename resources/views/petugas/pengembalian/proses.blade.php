@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Detail Pengembalian Alat</h2>

            <!-- INFO PEMINJAMAN -->
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div><span class="font-semibold text-gray-600">Peminjam:</span> {{ $peminjaman->user->name ?? '-' }}</div>
                <div><span class="font-semibold text-gray-600">Tanggal Pinjam:</span> {{ $peminjaman->tgl_pinjam }}</div>
                <div><span class="font-semibold text-gray-600">Batas Kembali:</span> {{ $peminjaman->tgl_kembali_plan }}
                </div>
                <div><span class="font-semibold text-gray-600">Tanggal Hari Ini:</span> {{ $tglSekarang->format('Y-m-d') }}
                </div>
            </div>

            <!-- ALERT TELAT -->
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

            <!-- ACTION FORM -->
            <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                <!-- TABEL ALAT DAN KONDISI PER ITEM -->
                <div class="mb-6">
                    <label class="font-semibold text-gray-700 block mb-2">Alat yang Dipinjam & Kondisi:</label>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 text-sm">
                                    <th class="p-3 border-b border-r">Nama Alat</th>
                                    <th class="p-3 border-b border-r text-center">Jumlah</th>
                                    <th class="p-3 border-b">Kondisi Saat Dikembalikan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peminjaman->detailPinjams as $detail)
                                    {{-- FILTER BARANG YANG DI ACC SAJA --}}
                                    @if (in_array($detail->status, ['disetujui', 'dipinjam']))
                                        <tr class="border-b">
                                            <td class="py-2 px-4">{{ $detail->alat->nama_alat }}</td>
                                            <td class="py-2 px-4 text-center">{{ $detail->jumlah }} pcs</td>
                                            <td class="py-2 px-4">
                                                <!-- Ini form select kondisi barang lu yang udah ada -->
                                                <select name="kondisi[{{ $detail->id }}]" class="...">
                                                    <option value="bagus">Bagus / Lengkap</option>
                                                    <option value="rusak">Rusak </option>
                                                    <option value="hilang">Hilang </option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INPUT DENDA TAMBAHAN -->
                <div class="mb-6 w-full md:w-1/2">
                    <label for="denda_tambahan" class="block text-gray-700 text-sm font-bold mb-2">Denda Lainnya / Tambahan
                        (Opsional)</label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 border border-gray-300 px-3 py-2 rounded-l text-gray-600 text-sm">Rp</span>
                        <input type="number" id="input_denda_tambahan" name="denda_tambahan" value="0" min="0"
                            class="w-full border border-gray-300 rounded-r px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">*Isi jika ada denda khusus. Biarkan 0 jika tidak ada.</p>
                </div>

                <!-- RINCIAN TOTAL DENDA -->
                <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="font-bold text-gray-700 mb-3 border-b pb-2">Rincian Total Denda</h3>

                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Denda Keterlambatan ({{ $telatHari }} hari)</span>
                        <span id="teks-denda-telat" data-telat="{{ $dendaOtomatis }}">Rp
                            {{ number_format($dendaOtomatis, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Total Denda Kondisi Barang</span>
                        <span id="teks-denda-kondisi">Rp 0</span>
                    </div>

                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Denda Tambahan</span>
                        <span id="teks-denda-tambahan">Rp 0</span>
                    </div>

                    <hr class="my-3 border-gray-300">

                    <div class="flex justify-between font-bold text-lg text-red-600">
                        <span>Total Denda Dibayar</span>
                        <span id="teks-total-denda">Rp {{ number_format($dendaOtomatis, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- HIDDEN INPUT TOTAL DENDA KESELURUHAN -->
                <input type="hidden" name="denda" id="input-denda" value="{{ $dendaOtomatis }}">

                <div class="flex gap-4">
                    <a href="{{ route('petugas.pengembalian.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg text-sm font-semibold transition text-center flex-none w-1/3 text-base pt-2.5">Batal</a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full flex-grow">
                        Verifikasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Javascript Kalkulator Otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua dropdown kondisi dan input tambahan
            const selectKondisis = document.querySelectorAll('.kondisi-select');
            const inputDendaTambahan = document.getElementById('input_denda_tambahan');

            // Ambil elemen teks untuk diubah
            const teksDendaKondisi = document.getElementById('teks-denda-kondisi');
            const teksDendaTambahan = document.getElementById('teks-denda-tambahan');
            const teksTotalDenda = document.getElementById('teks-total-denda');
            const inputHiddenDenda = document.getElementById('input-denda');

            // Ambil denda keterlambatan (statis)
            const dendaTelat = parseInt(document.getElementById('teks-denda-telat').getAttribute('data-telat')) ||
            0;

            // Format Rupiah function
            const formatRupiah = (angka) => {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            };

            // Fungsi utama hitung total
            function hitungTotal() {
                let totalDendaKondisi = 0;

                // Loop setiap dropdown barang yang dipilih
                selectKondisis.forEach(function(select) {
                    const dendaPerBarang = parseInt(select.options[select.selectedIndex].getAttribute(
                        'data-denda')) || 0;
                    totalDendaKondisi += dendaPerBarang;
                });

                // Ambil denda manual
                let dendaTambahan = parseInt(inputDendaTambahan.value);
                if (isNaN(dendaTambahan)) {
                    dendaTambahan = 0;
                }

                // Hitung grand total
                const grandTotal = dendaTelat + totalDendaKondisi + dendaTambahan;

                // Update text di HTML
                teksDendaKondisi.innerText = formatRupiah(totalDendaKondisi);
                teksDendaTambahan.innerText = formatRupiah(dendaTambahan);
                teksTotalDenda.innerText = formatRupiah(grandTotal);

                // Update hidden input untuk dikirim ke Controller
                inputHiddenDenda.value = grandTotal;
            }

            // Panggil hitungTotal tiap kali dropdown diubah atau input tambahan diketik
            selectKondisis.forEach(select => select.addEventListener('change', hitungTotal));
            inputDendaTambahan.addEventListener('input', hitungTotal);

            // Panggil sekali pas halaman baru beres loading (biar default nolnya ke-set)
            hitungTotal();
        });
    </script>
@endsection
