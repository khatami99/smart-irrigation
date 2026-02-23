<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Irrigation')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glow { box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800">

    {{-- Loading Bar (atas halaman) --}}
    <div id="loading-bar" class="fixed top-0 left-0 h-1 bg-blue-500 z-[9999] transition-all duration-300 ease-out" style="width: 0%; opacity: 0;"></div>

    {{-- Page Overlay (transisi antar halaman) --}}
    <div id="page-overlay" class="fixed inset-0 bg-white z-[998] flex items-center justify-center transition-opacity duration-300" style="opacity: 0; pointer-events: none;">
        <div class="flex flex-col items-center gap-4">
            <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-400 text-sm font-medium">Memuat halaman...</p>
        </div>
    </div>

    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <div class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-blue-400 italic">Smart<span class="text-white">Irrigate</span></h2>
            </div>
            <nav class="mt-2 px-4 flex-1">
                <a href="{{ route('irrigation') }}"
                   class="flex items-center p-3 rounded-lg mb-1 {{ request()->routeIs('irrigation') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-colors">
                    <span class="text-lg">📊</span>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>
                {{-- Tambah menu lain di sini nanti --}}
                {{-- <a href="{{ route('...') }}" class="flex items-center p-3 rounded-lg mb-1 ..."> --}}
            </nav>
            <div class="p-4 border-t border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Header --}}
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center border-b border-slate-100">
                <h1 class="text-xl font-semibold text-slate-700 underline decoration-blue-500 decoration-4 underline-offset-8">
                    @yield('page-title', 'Monitoring Panel')
                </h1>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase">System Status</p>
                        <p class="text-xs text-emerald-500 font-bold italic">● AI Model Active</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-500 transition font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-8 flex-1">

                {{-- Flash Message --}}
                @if (session('success'))
                    <div id="flash-message" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-5 py-4 mb-6 text-sm font-medium">
                        <span class="text-lg">✅</span>
                        {{ session('success') }}
                        <button onclick="document.getElementById('flash-message').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 text-lg leading-none">×</button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="flash-message" class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 rounded-xl px-5 py-4 mb-6 text-sm font-medium">
                        <span class="text-lg">❌</span>
                        {{ session('error') }}
                        <button onclick="document.getElementById('flash-message').remove()" class="ml-auto text-red-400 hover:text-red-600 text-lg leading-none">×</button>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')

    <script>
        // =====================
        // Loading Bar
        // =====================
        function startLoading() {
            const bar = document.getElementById('loading-bar');
            bar.style.opacity = '1';
            bar.style.width = '70%';
        }

        function finishLoading() {
            const bar = document.getElementById('loading-bar');
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => bar.style.width = '0%', 300);
            }, 200);
        }

        // =====================
        // Page Overlay Transition
        // =====================
        const overlay = document.getElementById('page-overlay');

        function showOverlay() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'all';
            startLoading();
        }

        function hideOverlay() {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            finishLoading();
        }

        // Sembunyikan overlay saat halaman selesai load
        window.addEventListener('load', hideOverlay);

        // Tampilkan overlay saat klik link navigasi (BUKAN pagination tabel)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (
                link &&
                link.href &&
                !link.closest('#table-wrapper') &&  // bukan pagination tabel
                !link.href.startsWith('#') &&        // bukan anchor
                !link.href.includes('?page=') &&     // bukan pagination URL
                !link.target &&
                link.hostname === window.location.hostname
            ) {
                showOverlay();
            }
        });

        // Tampilkan overlay saat form submit (logout, dll)
        document.addEventListener('submit', function() {
            showOverlay();
        });
    </script>

</body>
</html>
