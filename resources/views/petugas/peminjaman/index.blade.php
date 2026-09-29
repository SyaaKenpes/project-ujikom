@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Dashboard Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Menunggu Verifikasi Persetujuan </h3>
            <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <button type="submit"
                    class="group bg-slate-800 text-white px-5 py-2.5 rounded-r-xl font-medium transition-all duration-300 ease-in-out hover:bg-slate-900 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-300 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-12"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Cari</span>
                </button>
                @if (request('search'))
                    <a href="{{ route('petugas.peminjaman.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>


    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                    <th class="py-3 px-4 border-b">Detail Alat</th>
                    <th class="py-3 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $item)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            {{ $item->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                @foreach ($item->detailPinjams as $detail)
                                    <li>
                                        <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                        (Jumlah: {{ $detail->jumlah }})
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            @if ($item->status == 'diajukan')
                                <!-- 1. Tombol Trigger untuk Buka Modal -->
                                <button type="button"
                                    onclick="document.getElementById('modal-setujui-{{ $item->id }}').classList.remove('hidden')"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded font-semibold transition shadow-sm text-sm">
                                    Setujui
                                </button>

                                <!-- 2. Modal Pop-Up Tailwind -->
                                <div id="modal-setujui-{{ $item->id }}"
                                    class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex justify-center items-center text-left font-normal">
                                    <div class="relative mx-auto p-5 border w-[32rem] shadow-lg rounded-xl bg-white">

                                        <div class="mt-2">
                                            <h3 class="text-lg font-bold text-gray-900 border-b pb-2">Verifikasi Persetujuan
                                                Alat</h3>
                                            <p class="text-sm text-gray-500 mt-2 mb-4">
                                                Peminjam: <strong>{{ $item->user->name ?? 'User' }}</strong><br>
                                                Hilangkan centang jika barang tidak disetujui / stok kurang.
                                            </p>

                                            <!-- Form Persetujuan -->
                                            <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}"
                                                method="POST">
                                                @csrf

                                                <div class="max-h-60 overflow-y-auto pr-2">
                                                    @foreach ($item->detailPinjams as $detail)
                                                        <div
                                                            class="flex items-center justify-between mb-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                                            <div class="flex flex-col">
                                                                <span
                                                                    class="font-semibold text-gray-800 text-sm">{{ $detail->alat->nama_alat }}</span>
                                                                <span
                                                                    class="text-xs {{ $detail->alat->stok < $detail->jumlah ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                                                    Diminta: {{ $detail->jumlah }} | Stok:
                                                                    {{ $detail->alat->stok }}
                                                                </span>
                                                            </div>

                                                            <!-- Checkbox Barang -->
                                                            <input type="checkbox" name="approved_items[]"
                                                                value="{{ $detail->id }}"
                                                                class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                                                {{ $detail->alat->stok >= $detail->jumlah ? 'checked' : '' }}>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Tombol Action Modal -->
                                                <div class="flex justify-end gap-3 mt-6 pt-3 border-t">
                                                    <button type="button"
                                                        onclick="document.getElementById('modal-setujui-{{ $item->id }}').classList.add('hidden')"
                                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm font-medium">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium shadow-sm">
                                                        Konfirmasi & Disetujui
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada pengajuan peminjaman baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
@endsection
