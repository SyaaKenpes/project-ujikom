@extends('layouts.app') {{-- Pastikan mengarah ke layout utama sidebar lu --}}

@section('title', 'Profil Saya - GearHub')
@section('header-title', 'Profil Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500 text-emerald-600 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error Umum --}}
    @if ($errors->any())
        <div class="p-4 bg-red-500/10 border border-red-500 text-red-600 rounded-lg text-sm">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- KARTU KIRI: Foto Profil & Info Singkat --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 text-center flex flex-col items-center justify-center">
            <div class="relative w-28 h-28 mb-4">
                @if (auth()->user()->foto_profile)
                    <img src="{{ asset('images/profiles/' . auth()->user()->foto_profile) }}" alt="Foto Profil" class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow">
                @else
                    <div class="w-full h-full rounded-full bg-gray-800 text-white flex items-center justify-center border-4 border-gray-100 shadow">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <h2 class="text-lg font-bold text-gray-800">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-gray-500 mb-2">{{ auth()->user()->email }}</p>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full uppercase tracking-wider">
                {{ auth()->user()->role }}
            </span>
        </div>

        {{-- KARTU KANAN: Form Edit Data Diri --}}
        <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b">Edit Informasi Profil</h3>
            
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email (Tidak dapat diubah)</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled
                        class="w-full px-3 py-2 text-sm bg-gray-100 border rounded-lg text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', auth()->user()->no_hp) }}" required
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" required
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('alamat', auth()->user()->alamat) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ganti Foto Profil (Opsional)</label>
                    <input type="file" name="foto_profile" accept="image/png, image/jpeg, image/jpg"
                        class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <button type="submit" class="bg-[#00a86b] hover:bg-[#008f5a] text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow">
                    Simpan Perubahan
                </button>
            </form>
        </div>

    </div>

    {{-- KARTU BAWAH: Form Ganti Password --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <h3 class="text-base font-bold text-gray-800 mb-1">Keamanan & Ganti Password</h3>
        <p class="text-xs text-gray-500 mb-4">Masukkan password lama Anda untuk mengonfirmasi perubahan password baru.</p>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="current_password" required placeholder="••••••••••••"
                    class="w-full md:w-1/2 px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required placeholder="••••••••••••"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••••••"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow">
                Ubah Password
            </button>
        </form>
    </div>

</div>
@endsection