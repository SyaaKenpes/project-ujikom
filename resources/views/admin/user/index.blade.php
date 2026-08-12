@extends('layouts.app')

@section('header-title', 'Manajemen Penggunaan Sistem')

@section('content')
    <div class="p-6">
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">

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
                            class="bg-gray-800 hover:bg-gray-900 text-white text-sm px-4 py-1.5 rounded-lg">Cari</button>
                    </form>

                    <!-- Tombol Tambah User -->
                    <a href="{{ route('admin.user.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded-lg whitespace-nowrap">+
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
                                            class="bg-amber-500 hover:bg-amber-600 text-white text-xs px-3 py-1.5 rounded font-medium">Edit</a>
                                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded font-medium">Hapus</button>
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
