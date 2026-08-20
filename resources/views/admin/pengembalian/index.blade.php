@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Data Pengembalian Alat</h2>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Peminjam</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Alat yang Dikembalikan</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Kembali</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                {{-- Nanti kita isi data looping dari database di sini --}}
                <tr>
                    <td colspan="5" class="py-6 px-4 text-center text-sm text-gray-500 italic border-b">
                        Data pengembalian belum tersedia atau masih dalam proses.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection