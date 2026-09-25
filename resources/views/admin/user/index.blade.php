@extends('layouts.app')

@section('header-title', 'Manajemen Penggunaan Sistem')

@section('content')
    <div class="p-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <!-- Notifikasi Sukses -->
            @if (session('success'))
                <div
                    class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-md shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="font-medium text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Notifikasi Error (Buat jaga-jaga kalau gagal hapus) -->
            @if (session('error'))
                <div
                    class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <p class="font-medium text-sm">{{ session('error') }}</p>
                </div>
            @endif
            
            <!-- Header Card: Judul (Kiri) & Form Cari + Tombol Tambah (Kanan) -->
            <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Pengguna Sistem</h2>

                <div class="flex items-center gap-2">
                    <!-- Form Search -->
                    <form action="{{ route('admin.user.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, role..."
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="group bg-slate-800 text-white px-5 py-2.5 rounded-r-xl font-medium transition-all duration-300 ease-in-out hover:bg-slate-900 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-800/30 active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-300 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-12"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Cari</span>
                        </button>
                    </form>

                    <!-- Tombol Tambah User -->
                    <a href="{{ route('admin.user.create') }}"
                        class="inline-flex items-center gap-1 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:scale-95">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-90" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah User</a>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">NO</th>
                            <th class="px-4 py-3">NAMA</th>
                            <th class="px-4 py-3">EMAIL</th>
                            <th class="px-4 py-3 text-center">ROLE / HAK AKSES</th>
                            <th class="px-4 py-3">NO. HP</th>
                            <th class="px-4 py-3 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $index => $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $users->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($user->role == 'admin')
                                        <span
                                            class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-medium">Admin</span>
                                    @elseif($user->role == 'petugas')
                                        <span
                                            class="bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium">Petugas</span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Peminjam</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $user->no_hp ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.user.edit', $user->id) }}"
                                            class="inline-block bg-amber-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 ease-in-out hover:bg-amber-600 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/30 active:scale-95">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus user ini?')"
                                                class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 ease-in-out hover:bg-red-600 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-500/30 active:scale-95">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>

        </div>
    </div>
@endsection
