@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- STATS CARD GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 my-6">

        <!-- 1. Total Alat (Biru) -->
        <div
            class="group bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:shadow-blue-500/10 hover:border-blue-200 cursor-pointer">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Alat</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1 transition-colors duration-300 group-hover:text-blue-600">
                    {{ $totalAlat ?? 0 }}</h3>
                <span class="text-[10px] text-gray-400">Unit terdaftar</span>
            </div>
            <div
                class="p-3 bg-blue-100 text-blue-600 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>

        <!-- 2. Peminjaman Aktif (Kuning/Amber) -->
        <div
            class="group bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:shadow-amber-500/10 hover:border-amber-200 cursor-pointer">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedang Dipinjam</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1 transition-colors duration-300 group-hover:text-amber-600">
                    {{ $peminjamanAktif ?? 0 }}</h3>
                <span class="text-[10px] text-amber-600 font-medium">Masih di luar</span>
            </div>
            <div
                class="p-3 bg-amber-100 text-amber-600 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:-rotate-6 group-hover:bg-amber-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-amber-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
        </div>

        <!-- 3. Menunggu Persetujuan / Pending (Oren) -->
        <div
            class="group bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-200 cursor-pointer">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Perlu Persetujuan</p>
                <h3
                    class="text-2xl font-bold text-gray-800 mt-1 transition-colors duration-300 group-hover:text-orange-600">
                    {{ $totalPending ?? 0 }}</h3>
                <span class="text-[10px] text-orange-600 font-medium">Menunggu diapprove</span>
            </div>
            <div
                class="p-3 bg-orange-100 text-orange-600 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-orange-500 group-hover:text-white group-hover:shadow-lg group-hover:shadow-orange-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- 4. Alat Rusak / Maintenance (Merah) -->
        <div
            class="group bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:shadow-red-500/10 hover:border-red-200 cursor-pointer">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alat Rusak</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1 transition-colors duration-300 group-hover:text-red-600">
                    {{ $alatRusak ?? 0 }}</h3>
                <span class="text-[10px] text-red-500 font-medium">Tidak bisa dipinjam</span>
            </div>
            <div
                class="p-3 bg-red-100 text-red-600 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:-rotate-6 group-hover:bg-red-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-red-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- 5. Total User (Hijau/Emerald) -->
        <div
            class="group bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between transition-all duration-300 ease-in-out hover:-translate-y-1.5 hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-200 cursor-pointer">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total User</p>
                <h3
                    class="text-2xl font-bold text-gray-800 mt-1 transition-colors duration-300 group-hover:text-emerald-600">
                    {{ $totalUser ?? 0 }}</h3>
                <span class="text-[10px] text-emerald-600 font-medium">Pengguna terdaftar</span>
            </div>
            <div
                class="p-3 bg-emerald-100 text-emerald-600 rounded-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-emerald-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-emerald-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
        </div>

    </div>

    <!-- Tabel Log Aktivitas -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <!-- HEADER KOTAK DENGAN INPUT SEARCH -->
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Log Aktivitas Terbaru</h3>
            <!-- Form Search yang stylenya sama persis -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas / user..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
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
                    <a href="{{ route('admin.dashboard') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Waktu</th>
                        <th class="py-3 px-4 border-b">User</th>
                        <th class="py-3 px-4 border-b">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b">{{ $log->created_at }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $log->user->name ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4 border-b">{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Tidak ada log aktivitas yang
                                ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
