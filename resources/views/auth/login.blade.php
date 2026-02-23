<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Irrigation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">

    {{-- Loading Bar --}}
    <div id="loading-bar" class="fixed top-0 left-0 h-1 bg-emerald-500 z-[9999] transition-all duration-300 ease-out" style="width: 0%; opacity: 0;"></div>

    {{-- Page Overlay --}}
    <div id="page-overlay" class="fixed inset-0 bg-gray-900 z-[998] flex items-center justify-center transition-opacity duration-300" style="opacity: 0; pointer-events: none;">
        <div class="flex flex-col items-center gap-4">
            <div class="w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-gray-400 text-sm font-medium">Memuat halaman...</p>
        </div>
    </div>

    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-emerald-400">💧 Smart Irrigation</h1>
            <p class="text-gray-400 mt-2">Masuk ke akun kamu untuk melanjutkan</p>
        </div>

        <div class="bg-gray-800 rounded-2xl shadow-xl p-8">

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg px-4 py-3 mb-6 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-lg px-4 py-3 mb-6 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input
                        type="email" id="email" name="email"
                        value="{{ old('email') }}" required autofocus
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent placeholder-gray-500 @error('email') border-red-500 @enderror"
                        placeholder="contoh@email.com"
                    >
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input
                        type="password" id="password" name="password" required
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent placeholder-gray-500"
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 text-emerald-500 bg-gray-700 border-gray-600 rounded focus:ring-emerald-500">
                    <label for="remember" class="ml-2 text-sm text-gray-400">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-lg transition duration-200 text-sm">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            Smart Irrigation System &copy; {{ date('Y') }}
        </p>
    </div>

    <script>
        const overlay = document.getElementById('page-overlay');
        const bar = document.getElementById('loading-bar');

        function showOverlay() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'all';
            bar.style.opacity = '1';
            bar.style.width = '70%';
        }

        function hideOverlay() {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => bar.style.width = '0%', 300);
            }, 200);
        }

        // Sembunyikan overlay saat halaman selesai load
        window.addEventListener('load', hideOverlay);

        // Tampilkan overlay saat form login disubmit
        document.addEventListener('submit', function() {
            showOverlay();
        });
    </script>

</body>
</html>
