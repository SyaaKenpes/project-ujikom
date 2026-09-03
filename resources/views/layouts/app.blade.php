<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside
            class="w-64 bg-gray-900 text-white flex flex-col justify-between hidden md:flex shrink-0 border-r border-gray-800">

            <!-- Bagian Atas: Brand & Menu Navigation -->
            <div>
                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                    PANEL ADMIN
                </div>

                <nav class="p-4 space-y-3">
                    <!-- Menu khusus admin -->
                    <!-- Dashboard -->
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                            Dashboard
                        </a>

                        <!-- Kelola User -->
                        <a href="{{ route('admin.user.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.user.*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Kelola User
                        </a>

                        <!-- Kelola Kategori -->
                        <a href="{{ route('admin.kategori.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.kategori.*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                            Kelola Kategori
                        </a>

                        <!-- Kelola Alat -->
                        <a href="{{ route('admin.alat.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.alat.*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                            Kelola Alat
                        </a>

                        <!-- Kelola Peminjaman -->
                        <a href="{{ route('admin.peminjaman.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.peminjaman.*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            Kelola Peminjaman
                        </a>

                        <!-- Kelola Pengembalian -->
                        <a href="{{ route('admin.pengembalian.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('admin.pengembalian.*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Kelola Pengembalian
                        </a>

                        <!-- Menu khusus petugas -->
                    @elseif (auth()->user()->role === 'petugas')
                        <a href="{{ route('petugas.peminjaman.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('petugas.peminjaman*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Persetujuan Peminjaman
                        </a>

                        <a href="{{ route('petugas.pengembalian.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('petugas.pengembalian*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Pemantauan Pengembalian
                        </a>

                        <a href="{{ route('petugas.laporan.index') }}"
                            class="group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-300 ease-out {{ request()->routeIs('petugas.laporan*') ? 'bg-slate-800 border-l-4 border-blue-500 text-white font-semibold shadow-md translate-x-1' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white hover:translate-x-2' }}">
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            Cetak Laporan
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Bagian Bawah: Logged In Status -->
            <div class="p-4 border-t border-gray-800 text-xs text-gray-400">
                Logged in as: <span class="text-white font-semibold">{{ auth()->user()->name ?? 'User' }}</span>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header
                class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10 border-b border-gray-200">
                <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Dashboard')
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
