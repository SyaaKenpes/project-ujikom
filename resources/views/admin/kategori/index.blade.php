@extends('layouts.app')

@section('title', 'Kelola Kategori - Panel Admin')
@section('header-title', 'Manajemen Kategori Alat')

@section('content')
    <!-- Notifikasi -->
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
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Kategori Alat</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Form Search -->
                <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
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
                        <a href="{{ route('admin.kategori.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Tombol Tambah -->
                <a href="{{ route('admin.kategori.create') }}"
                    class="inline-flex items-center gap-1 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 ease-in-out hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:scale-95">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-90" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>Tambah Kategori
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b w-16 text-center">No</th>
                        <th class="py-3 px-4 border-b">Nama Kategori</th>
                        <th class="py-3 px-4 border-b w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($kategoris as $index => $kategori)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b text-center">{{ $kategoris->firstItem() + $index }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $kategori->nama_kategori }}</td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.kategori.edit', $kategori->id) }}"
                                        class="inline-block bg-amber-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 ease-in-out hover:bg-amber-600 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/30 active:scale-95">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300 ease-in-out hover:bg-red-600 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-red-500/30 active:scale-95">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $kategoris->links() }}
        </div>
    </div>
@endsection
