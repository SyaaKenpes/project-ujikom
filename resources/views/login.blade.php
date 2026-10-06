<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GearHub</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS untuk Animasi -->
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
</head>

<body
    class="min-h-screen bg-cover bg-center bg-no-repeat relative flex flex-col justify-between items-center p-4 font-sans"
    style="background-image: url('{{ asset('images/bg-login.jpeg') }}');">

    <!-- Overlay Gelap pada Background -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div <!-- Spacer Atas -->
    <div></div>

    <!-- Card Login Utama (Ditambah class animate-fade-in-up) -->
    <div
        class="relative z-10 w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 my-auto animate-fade-in-up">

        <!-- Sisi Kiri: Form Login (Diubah jadi bg-black/60 dan backdrop-blur-md) -->
        <div
            class="p-8 sm:p-10 flex flex-col justify-between bg-black/60 backdrop-blur-md text-white border-y border-l border-white/10 rounded-l-3xl">
            <div>
                <h1 class="text-2xl font-bold tracking-wide mb-6 text-white drop-shadow-sm">
                    Selamat datang
                </h1>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-500/80 backdrop-blur-sm rounded-lg text-xs text-white">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-500 text-white p-3 rounded-md mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Input Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-200">Email</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="Contoh@gmail.com"
                                class="w-full pl-9 pr-3 py-2 bg-white/90 text-gray-900 placeholder-gray-500 text-sm rounded-lg border border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 transition shadow-inner">
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-200">Password</label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </span>
                            <input type="password" name="password" required placeholder="••••••••••••"
                                class="w-full pl-9 pr-3 py-2 bg-white/90 text-gray-900 placeholder-gray-500 text-sm rounded-lg border border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 transition shadow-inner">
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-300 pt-1">
                        Masukkan email dan password untuk mengakses sistem.
                    </p>

                    <!-- Tombol Login (Ditambah hover transform) -->
                    <button type="submit"
                        class="w-full mt-2 bg-[#00a86b] hover:bg-[#008f5a] text-white font-semibold py-2 px-4 rounded-lg text-sm transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-[#00a86b]/40">
                        Masuk
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center text-xs">
                <p class="text-gray-300">Belum punya akun?</p>
                <a href="#" class="text-sky-300 hover:text-sky-200 font-medium transition underline">Hubungi
                    Admin</a>
            </div>
        </div>

        <!-- Sisi Kanan: Branding Panel -->
        <div
            class="bg-[#0b335c] p-8 sm:p-10 flex flex-col items-center justify-center text-center text-white rounded-r-3xl">

            <div class="w-50 h-50 mb-2 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="GearHub Logo" class="w-full h-full object-contain">
            </div>

            <h2 class="text-2xl font-bold tracking-wide">GearHub</h2>
            <p class="text-xs text-sky-200 mt-1 font-medium mb-5">Aplikasi Peminjaman alat</p>

            <div class="text-xs text-gray-200 max-w-xs">
                <p class="leading-relaxed text-gray-300 mb-2">
                    Kelola peminjaman dan pengembalian alat sekolah yang:
                </p>

                <div class="flex flex-col items-start pl-6 space-y-1.5 text-left font-medium">
                    <div class="flex items-center gap-2">
                        <span class="bg-[#00a86b] text-white rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Cepat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-[#00a86b] text-white rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Praktis</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-[#00a86b] text-white rounded-full p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </span>
                        <span>Terdata</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Footer -->
    <div class="relative z-10 text-center py-2">
        <p class="text-xs text-gray-300 tracking-wider font-light drop-shadow">
            &copy; 2026 GearHub_7 V1.0
        </p>
    </div>

</body>

</html>
