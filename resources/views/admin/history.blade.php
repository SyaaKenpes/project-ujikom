@extends('layouts.app') <!-- Sesuaikan sama layout utama lu -->

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Kelola History Peminjaman</h2>

        <!-- Bagian Filter & Tombol PDF -->
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex justify-between items-center">

            <!-- Form Filter Tanggal -->
            <form action="{{ route('admin.history') }}" method="GET" class="flex items-center gap-4">
                <div>
                    <label class="text-sm font-semibold text-gray-600">Dari Tanggal:</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="border p-2 rounded w-40">
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-600">Sampai:</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="border p-2 rounded w-40">
                </div>
                <button type="submit"
                    class="mt-5 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
                <a href="{{ route('admin.history') }}"
                    class="mt-5 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Reset</a>
            </form>

            <!-- Tombol Cetak PDF Dinamis (Membawa parameter tanggal dari URL) -->
            <a href="{{ route('admin.laporan.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 mt-5">
                📥 Cetak Laporan PDF
            </a>
        </div>

        <!-- Tabel History -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border-b">Peminjam</th>
                        <th class="p-3 border-b">Tanggal Kembali</th>
                        <th class="p-3 border-b">Kondisi</th>
                        <th class="p-3 border-b">Denda</th>
                        <th class="p-3 border-b">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $item->peminjaman->user->name ?? '-' }}</td>
                            <td class="p-3 border-b">{{ $item->tgl_kembali }}</td>
                            <td class="p-3 border-b">{{ $item->kondisi_kembali }}</td>
                            <td class="p-3 border-b">Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                            <td class="p-3 border-b">{{ $item->petugas->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tombol Pagination -->
        <div class="mt-4">
            {{ $histories->links() }}
        </div>
    </div>
@endsection
