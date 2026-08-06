@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')
    <!-- Notifikasi Sukses/Gagal -->
    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>
            <!-- Tombol Tambah User (jika ingin dibuatkan form tambah) -->
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Nama</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Role / Hak Akses</th>
                        <th class="py-3 px-4 border-b">No. HP</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="py-3 px-4 border-b">{{ $user->email }}</td>
                            <td class="py-3 px-4 border-b">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full
                    @if ($user->role == 'admin') bg-purple-100 text-purple-800
                    @elseif($user->role == 'petugas') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">{{ $user->no_hp ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">
                                <span class="text-gray-400 text-xs italic">Kelola Data</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
