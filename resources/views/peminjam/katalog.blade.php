@extends('layouts.app')

@section('header-title', 'Katalog Alat & Pengajuan Peminjaman')

@section('content')
    <div class="space-y-6">

        @if (session('error'))
            <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- BAGIAN 1: FORM FILTER KATEGORI -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('peminjam.katalog.index') }}" method="GET"
                class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full md:w-1/3">
                    <label for="kategori_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        Filter Kategori
                    </label>

                    <!-- Tambahin onchange="this.form.submit()" di sini -->
                    <select name="kategori_id" id="kategori_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- BAGIAN 2: FORM PENGAJUAN PINJAM -->
        <form action="{{ route('peminjam.peminjaman.store') }}" method="POST">
            @csrf


            {{-- Alert Error Validasi Laravel --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <p class="font-bold text-sm">Gagal Mengajukan!</p>
                    </div>
                    <ul class="list-disc list-inside text-sm pl-7">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Top Card: Form Rencana Pengembalian & Submit Button -->
            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="w-full md:w-1/2">
                    <label for="tgl_kembali_plan" class="block text-sm font-semibold text-gray-700 mb-1">
                        Rencana Tanggal Pengembalian
                    </label>
                    <input type="date" name="tgl_kembali_plan" id="tgl_kembali_plan" min="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div class="w-full md:w-auto flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg shadow transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Ajukan Peminjaman
                    </button>
                </div>
            </div>

            <!-- Grid Daftar Alat -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mt-6">
                @forelse($alats as $alat)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between p-4 hover:shadow-md transition">
                        <div>
                            <!-- AREA FOTO BARANG -->
                            <div
                                class="w-full h-40 bg-gray-100 rounded-lg mb-3 overflow-hidden border border-gray-200 flex items-center justify-center">
                                @if ($alat->gambar)
                                    <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Tidak ada foto</span>
                                @endif
                            </div>

                            <!-- BADGE KATEGORI & KONDISI -->
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-[11px] font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded-md truncate max-w-[60%]">
                                    {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                                </span>

                                <!-- Bikin warna kondisi dinamis (Hijau buat baik, Merah buat rusak, dll) -->
                                @php
                                    $kondisi = strtolower($alat->kondisi);
                                    $color =
                                        $kondisi == 'baik'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : ($kondisi == 'rusak'
                                                ? 'bg-red-100 text-red-700'
                                                : 'bg-blue-50 text-blue-600');
                                @endphp
                                <span class="text-[11px] font-bold px-2 py-1 rounded-md {{ $color }}">
                                    {{ ucfirst($alat->kondisi) }}
                                </span>
                            </div>

                            <!-- INFO ALAT -->
                            <h3 class="text-sm font-bold text-gray-800 mb-1 leading-tight">{{ $alat->nama_alat }}</h3>
                            <p class="text-xs text-gray-500 mb-4">Stok Tersedia: <strong
                                    class="text-gray-800 text-sm">{{ $alat->stok }}</strong></p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}"
                                    class="alat-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                    data-target="#jumlah_{{ $alat->id }}">
                                <span class="text-sm font-semibold text-gray-700">Pilih Alat Ini</span>
                            </label>

                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Jumlah Dipinjam</label>
                                <input type="number" name="jumlah[]" id="jumlah_{{ $alat->id }}" value="1"
                                    min="1" max="{{ $alat->stok }}" disabled required
                                    class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-gray-50 disabled:opacity-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-xl border border-gray-200">
                        <p class="text-gray-500 font-medium">Yah, belum ada alat yang tersedia di kategori ini.</p>
                    </div>
                @endforelse
            </div>
        </form>

    </div>

    <script>
        document.querySelectorAll('.alat-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const targetInput = document.querySelector(this.dataset.target);
                if (this.checked) {
                    targetInput.disabled = false;
                    targetInput.classList.remove('bg-gray-50');
                } else {
                    targetInput.disabled = true;
                    targetInput.classList.add('bg-gray-50');
                }
            });
        });
    </script>
@endsection
