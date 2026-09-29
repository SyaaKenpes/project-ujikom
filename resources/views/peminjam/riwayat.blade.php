@extends('layouts.app')

@section('header-title', 'Riwayat Peminjaman Saya')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Daftar Transaksi</h2>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded-r shadow-sm">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Tgl Pengajuan</th>
                        <th class="px-6 py-3.5">Rencana Kembali</th>
                        <th class="px-6 py-3.5">Detail Alat</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($peminjamans as $pinjam)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-500">#{{ $pinjam->id }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($pinjam->tgl_kembali_plan)->format('d M Y') }}</td>
                            
                            {{-- KOLOM DETAIL ALAT YANG SUDAH DIUPDATE --}}
                            <td class="px-6 py-4">
                                <ul class="space-y-1.5 text-sm">
                                    @foreach($pinjam->detailPinjams as $detail)
                                        <li class="flex items-center gap-2 text-gray-800 font-medium">
                                            <span>
                                                • {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} 
                                                <span class="text-xs text-gray-500 font-normal">({{ $detail->jumlah }} pcs)</span>
                                            </span>
                                            
                                            {{-- STATUS 1: DISETUJUI / ACC (Badge Biru / Ceklis) --}}
                                            @if($detail->status === 'disetujui')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">
                                                    <svg class="w-3 h-3 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    ACC
                                                </span>

                                            {{-- STATUS 2: DITOLAK / STOK HABIS (Badge Merah / Silang) --}}
                                            @elseif($detail->status === 'dibatalkan_stok_habis' || $detail->status === 'ditolak')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">
                                                    <svg class="w-3 h-3 mr-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Ditolak
                                                </span>

                                            {{-- STATUS 3: MASIH MENUNGGU PERSETUJUAN (Badge Kuning) --}}
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-700">
                                                    Menunggu
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- KOLOM STATUS UTAMA PEMINJAMAN --}}
                            <td class="px-6 py-4">
                                @php
                                    $st = strtolower($pinjam->status);
                                @endphp

                                @if($st == 'diajukan' || $st == 'menunggu persetujuan')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif($st == 'dipinjam')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        Sedang Dipinjam
                                    </span>
                                @elseif($st == 'dikembalikan')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ ucfirst($pinjam->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                Belum ada riwayat peminjaman. Yuk, mulai pinjam alat!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection