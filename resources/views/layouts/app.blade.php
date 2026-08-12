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
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white font-semibold shadow-sm' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white' }}">
                        Dashboard
                    </a>

                    <!-- Kelola User -->
                    <a href="{{ route('admin.user.index') }}"
                        class="block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.user.*') ? 'bg-slate-800 text-white font-semibold shadow-sm' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white' }}">
                        Kelola User
                    </a>

                    <!-- Kelola Kategori -->
                    <a href="{{ route('admin.kategori.index') }}"
                        class="block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.kategori.*') ? 'bg-slate-800 text-white font-semibold shadow-sm' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white' }}">
                        Kelola Kategori
                    </a>

                    <!-- Kelola Alat -->
                    <a href="{{ route('admin.alat.index') }}"
                        class="block px-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.alat.*') ? 'bg-slate-800 text-white font-semibold shadow-sm' : 'text-gray-400 hover:bg-slate-800/60 hover:text-white' }}">
                        Kelola Alat
                    </a>
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
