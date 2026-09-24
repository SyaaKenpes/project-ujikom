@extends('layouts.app')

@section('title', 'Kelola Kategori - Panel Admin')
@section('header-title', 'Manajemen Pengembalian')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <!-- TABEL 1: BARANG YANG HARUS DIKEMBALIKAN -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Proses Pengembalian (Status: Dipinjam)</h2>

            <!-- Bungkus pake div flex biar posisinya bisa sejajar rapi kalau mau ditambahin tombol lain di kirinya -->
            <div class="flex justify-end mb-4">
                <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <a href="{{ route('admin.pengembalian.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <!-- Header Tabel disamakan dengan Kelola Peminjaman -->
                    <thead class="text-sm text-gray-600 uppercase bg-white border-b border-gray-200">
                        <tr>
                            <th class="py-4 px-6 font-bold">PEMINJAM</th>
                            <th class="py-4 px-6 font-bold">TANGGAL PINJAM</th>
                            <th class="py-4 px-6 font-bold">BATAS KEMBALI</th>
                            <th class="py-4 px-6 font-bold text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($sedangDipinjam as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium">{{ $item->user->name }}</td>
                                <td class="py-4 px-6">{{ $item->tgl_pinjam }}</td>
                                <td class="py-4 px-6">{{ $item->tgl_kembali_plan }}</td>
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('admin.pengembalian.create', $item->id) }}"
                                        class="inline-flex items-center gap-1 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:scale-95">
                                        Proses Pengembalian
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-500 italic">
                                    Tidak ada barang yang sedang dipinjam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
