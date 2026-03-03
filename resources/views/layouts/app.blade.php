<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Irrigation')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&family=Karla:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/favicon_io/favicon.ico?v=' . time()) }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon_io/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon_io/apple-touch-icon.png') }}">

    <style>
        :root {
            --soil:   #3d2b1f;
            --soil2:  #5c3d2e;
            --earth:  #8b5e3c;
            --clay:   #c4895a;
            --straw:  #e8d5a3;
            --cream:  #faf6ef;
            --cream2: #f5ede0;
            --water:  #4a7c6f;
            --water2: #6aab9a;
            --leaf:   #5a7a47;
            --text:   #4a3728;
            --textlt: #7a6355;
            --border: rgba(139,94,60,.14);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Karla', sans-serif;
            background: var(--cream2);
            color: var(--text);
        }
        h1, h2, h3, .font-serif { font-family: 'Fraunces', serif; }

        /* Sidebar */
        .sidebar { background: var(--soil); }
        .sidebar-logo { font-family: 'Fraunces', serif; }
        .nav-item {
            display: flex; align-items: center;
            padding: .65rem 1rem; border-radius: 8px;
            font-size: .875rem; font-weight: 500;
            color: rgba(232,213,163,.6);
            transition: background .2s, color .2s;
            text-decoration: none; margin-bottom: 2px;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: var(--straw); }
        .nav-item.active { background: rgba(106,171,154,.2); color: var(--water2); }

        /* Loading bar */
        #loading-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: linear-gradient(90deg, var(--water), var(--water2));
            z-index: 9999; transition: all .3s ease-out;
        }

        /* Page overlay */
        #page-overlay {
            position: fixed; inset: 0; background: var(--cream);
            z-index: 998; display: flex; align-items: center; justify-content: center;
            transition: opacity .3s;
        }

        /* Header */
        .app-header {
            background: var(--cream); border-bottom: 1px solid var(--border);
        }

        /* Flash messages */
        .flash-success {
            background: rgba(90,122,71,.08); border: 1px solid rgba(90,122,71,.25);
            color: var(--leaf); border-radius: 10px; padding: .9rem 1.25rem;
            font-size: .875rem; font-weight: 500; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: .75rem;
        }
        .flash-error {
            background: rgba(185,74,60,.08); border: 1px solid rgba(185,74,60,.25);
            color: #b94a3c; border-radius: 10px; padding: .9rem 1.25rem;
            font-size: .875rem; font-weight: 500; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: .75rem;
        }
        .flash-close {
            margin-left: auto; background: none; border: none;
            cursor: pointer; font-size: 1.1rem; opacity: .5; transition: opacity .2s;
        }
        .flash-close:hover { opacity: 1; }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Loading Bar --}}
    <div id="loading-bar" style="width:0%;opacity:0;"></div>

    {{-- Page Overlay --}}
    <div id="page-overlay" style="opacity:0;pointer-events:none;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;">
            <div style="width:36px;height:36px;border:3px solid var(--water2);border-top-color:transparent;border-radius:50%;animation:spin .8s linear infinite;"></div>
            <p style="font-size:.875rem;color:var(--textlt);font-family:'Karla',sans-serif;">Memuat halaman...</p>
        </div>
    </div>

    <style>@keyframes spin { to { transform: rotate(360deg); } }</style>

    <div style="min-height:100vh;display:flex;">

        {{-- Sidebar --}}
        <div class="sidebar" style="width:240px;display:flex;flex-direction:column;flex-shrink:0;">

            {{-- Logo --}}
            <div style="padding:1.5rem 1.25rem;border-bottom:1px solid rgba(255,255,255,.07);">
                <div class="sidebar-logo" style="font-size:1.15rem;font-weight:700;color:var(--straw);letter-spacing:-.01em;">
                    💧 Smart<span style="color:var(--water2);font-style:italic;font-weight:300;">Irigasi</span>
                </div>
                <p style="font-size:.7rem;color:rgba(232,213,163,.35);margin-top:.25rem;letter-spacing:.06em;text-transform:uppercase;">Monitoring Panel</p>
            </div>

            {{-- Nav --}}
            <nav style="padding:1rem .75rem;flex:1;">
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(232,213,163,.3);padding:.5rem .5rem;margin-bottom:.25rem;">Menu</p>
                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">📊</span> Dashboard
                </a>
                <a href="{{ route('irrigation.index') }}"
                   class="nav-item {{ request()->routeIs('irrigation.index') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">🗃️</span> Data Iklim
                </a>
                @can('view petak')
                <a href="{{ route('petak.index') }}"
                class="nav-item {{ request()->routeIs('petak.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">🗺️</span> Master Petak
                </a>
                @endcan

                @can('view musim-tanam')
                <a href="{{ route('musim-tanam.index') }}"
                class="nav-item {{ request()->routeIs('musim-tanam.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">🌱</span> Musim Tanam
                </a>
                @endcan

                @can('view blangko-op')
                <a href="{{ route('blangko-op.index') }}"
                class="nav-item {{ request()->routeIs('blangko-op.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">📋</span> Blangko OP
                </a>
                @endcan

                <a href="{{ route('grafik.index') }}"
                class="nav-item {{ request()->routeIs('grafik.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">📈</span> Grafik & Analisis
                </a>

                @can('view rtt')
                <a href="{{ route('rtt.index') }}"
                class="nav-item {{ request()->routeIs('rtt.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">🗓️</span> RTT
                </a>
                @endcan

                @can('view peta')
                <a href="{{ route('peta.index') }}"
                class="nav-item {{ request()->routeIs('peta.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">🗺️</span> Peta Irigasi
                </a>
                @endcan

                <a href="{{ route('laporan.index') }}"
                class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <span style="font-size:1rem;margin-right:.65rem;">📑</span> Laporan
                </a>
            </nav>

            {{-- User --}}
            <div style="padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.07);">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:34px;height:34px;border-radius:50%;background:rgba(106,171,154,.25);border:1px solid rgba(106,171,154,.3);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-weight:700;color:var(--water2);font-size:.9rem;flex-shrink:0;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div style="min-width:0;flex:1;">
                        <p style="font-size:.8rem;font-weight:600;color:var(--straw);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name }}</p>
                        <p style="font-size:.68rem;color:rgba(232,213,163,.4);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-top:.75rem;">
                    @csrf
                    <button type="submit" style="width:100%;padding:.45rem;background:rgba(185,74,60,.12);border:1px solid rgba(185,74,60,.2);color:rgba(232,213,163,.5);border-radius:6px;font-size:.75rem;font-family:'Karla',sans-serif;cursor:pointer;transition:all .2s;"
                        onmouseover="this.style.background='rgba(185,74,60,.22)';this.style.color='#e8a090';"
                        onmouseout="this.style.background='rgba(185,74,60,.12)';this.style.color='rgba(232,213,163,.5)';">
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Main --}}
        <div style="flex:1;display:flex;flex-direction:column;min-width:0;">

            {{-- Header --}}
            <header class="app-header" style="padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--textlt);margin-bottom:.2rem;">Smart Irrigation System</p>
                    <h1 style="font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;color:var(--soil);letter-spacing:-.02em;">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:34px;height:34px;border-radius:50%;background:rgba(106,171,154,.25);border:1px solid rgba(106,171,154,.3);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-weight:700;color:var(--water2);font-size:.9rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:.8rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name }}</p>
                        <p style="font-size:.68rem;color:var(--textlt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </header>

            <style>@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }</style>

            {{-- Content --}}
            <main style="padding:2rem;flex:1;">

                @if(session('success'))
                    <div class="flash-success" id="flash-msg">
                        <span>✓</span> {{ session('success') }}
                        <button class="flash-close" onclick="document.getElementById('flash-msg').remove()">×</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash-error" id="flash-msg">
                        <span>✕</span> {{ session('error') }}
                        <button class="flash-close" onclick="document.getElementById('flash-msg').remove()">×</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        function startLoading() {
            const bar = document.getElementById('loading-bar');
            bar.style.opacity = '1'; bar.style.width = '70%';
        }
        function finishLoading() {
            const bar = document.getElementById('loading-bar');
            bar.style.width = '100%';
            setTimeout(() => { bar.style.opacity = '0'; setTimeout(() => bar.style.width = '0%', 300); }, 200);
        }
        const overlay = document.getElementById('page-overlay');
        function showOverlay() { overlay.style.opacity='1'; overlay.style.pointerEvents='all'; startLoading(); }
        function hideOverlay() { overlay.style.opacity='0'; overlay.style.pointerEvents='none'; finishLoading(); }

        window.addEventListener('load', hideOverlay);

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.closest('#table-wrapper') && !link.href.startsWith('#') && !link.href.includes('?page=') && !link.target && link.hostname === window.location.hostname) {
                showOverlay();
            }
        });
        document.addEventListener('submit', function(e) {
            // Jangan overlay untuk form hapus (inline di tabel)
            if (!e.target.closest('#table-wrapper')) showOverlay();
        });
    </script>

</body>
</html>
