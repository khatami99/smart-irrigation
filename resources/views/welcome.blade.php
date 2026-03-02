<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation System — Irigasi Rawa Pintar Berbasis AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&family=Karla:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --soil:    #3d2b1f;
            --soil2:   #5c3d2e;
            --earth:   #8b5e3c;
            --clay:    #c4895a;
            --straw:   #e8d5a3;
            --cream:   #faf6ef;
            --water:   #4a7c6f;
            --water2:  #6aab9a;
            --leaf:    #5a7a47;
            --leaf2:   #7da55f;
            --text:    #4a3728;
            --textlt:  #7a6355;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Karla', sans-serif;
            background: var(--cream);
            color: var(--text);
            overflow-x: hidden;
        }

        h1, h2, h3 { font-family: 'Fraunces', serif; }

        /* Grain texture overlay */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 998;
        }

        /* ── Nav ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 1.2rem 3rem;
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(250,246,239,0.88);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(139,94,60,.12);
            transition: all .4s;
        }
        .nav-logo {
            font-family: 'Fraunces', serif;
            font-size: 1.1rem; font-weight: 700;
            color: var(--soil); letter-spacing: -.01em;
        }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a {
            font-size: .85rem; font-weight: 500; color: var(--textlt);
            text-decoration: none; transition: color .2s;
        }
        .nav-links a:hover { color: var(--water); }
        .nav-cta {
            background: var(--soil); color: var(--straw);
            padding: .55rem 1.4rem; border-radius: 6px;
            font-size: .85rem; font-weight: 600;
            transition: background .2s, transform .2s;
        }
        .nav-cta:hover { background: var(--soil2); transform: translateY(-1px); }

        /* ── Hero ── */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 7rem 3rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
            gap: 4rem;
        }

        @media (max-width: 768px) {
            .hero { grid-template-columns: 1fr; padding: 7rem 1.5rem 3rem; gap: 2rem; }
            .hero-visual { display: none; }
        }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: .5rem;
            font-size: .75rem; font-weight: 600; letter-spacing: .12em;
            text-transform: uppercase; color: var(--water);
            margin-bottom: 1.5rem;
            animation: fadeUp .7s ease both;
        }
        .hero-eyebrow::before {
            content: '';
            display: inline-block; width: 28px; height: 2px;
            background: var(--water2);
        }

        .hero-title {
            font-size: clamp(2.8rem, 5vw, 4.5rem);
            font-weight: 700; line-height: 1.08;
            letter-spacing: -.02em; color: var(--soil);
            margin-bottom: 1.5rem;
            animation: fadeUp .7s .1s ease both;
        }
        .hero-title em {
            font-style: italic; color: var(--water);
            font-weight: 300;
        }

        .hero-sub {
            font-size: 1.05rem; font-weight: 300; color: var(--textlt);
            line-height: 1.75; max-width: 480px; margin-bottom: 2.5rem;
            animation: fadeUp .7s .2s ease both;
        }

        .hero-btns {
            display: flex; gap: 1rem; flex-wrap: wrap;
            animation: fadeUp .7s .3s ease both;
        }

        .btn-primary {
            background: var(--soil); color: var(--straw);
            padding: .9rem 2rem; border-radius: 6px;
            font-family: 'Karla', sans-serif; font-weight: 600; font-size: .95rem;
            text-decoration: none; transition: background .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(61,43,31,.2);
        }
        .btn-primary:hover { background: var(--soil2); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(61,43,31,.3); }

        .btn-outline {
            border: 1.5px solid var(--clay); color: var(--earth);
            padding: .9rem 2rem; border-radius: 6px;
            font-family: 'Karla', sans-serif; font-weight: 600; font-size: .95rem;
            text-decoration: none; transition: all .2s;
        }
        .btn-outline:hover { background: rgba(196,137,90,.08); border-color: var(--earth); transform: translateY(-2px); }

        /* Hero visual — illustrated field */
        .hero-visual {
            position: relative;
            animation: fadeUp .7s .15s ease both;
        }

        .field-illustration {
            width: 100%; aspect-ratio: 4/3;
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 24px 80px rgba(61,43,31,.18), 0 4px 16px rgba(61,43,31,.1);
            position: relative;
            background: linear-gradient(180deg,
                #c8e6f0 0%, #a8d8ea 25%,
                #7db87a 40%, #5a8a52 50%,
                #8b9e5a 55%, #6b7a3d 65%,
                #c4a882 80%, #b8956a 100%
            );
        }

        /* Sky clouds */
        .cloud {
            position: absolute; top: 8%;
            background: rgba(255,255,255,.75);
            border-radius: 50px;
            animation: cloudDrift linear infinite;
        }
        .cloud::before, .cloud::after {
            content: ''; position: absolute;
            background: rgba(255,255,255,.75);
            border-radius: 50%;
        }
        .cloud-1 { width: 80px; height: 24px; left: 10%; animation-duration: 25s; animation-delay: 0s; }
        .cloud-1::before { width: 40px; height: 36px; top: -18px; left: 10px; }
        .cloud-1::after  { width: 28px; height: 28px; top: -14px; left: 35px; }
        .cloud-2 { width: 60px; height: 18px; left: 55%; top: 14%; animation-duration: 32s; animation-delay: -10s; }
        .cloud-2::before { width: 30px; height: 28px; top: -14px; left: 8px; }
        .cloud-2::after  { width: 22px; height: 22px; top: -11px; left: 28px; }

        @keyframes cloudDrift {
            from { transform: translateX(-120px); }
            to   { transform: translateX(calc(100vw + 120px)); }
        }

        /* Water layer */
        .water-layer {
            position: absolute; bottom: 30%; left: 0; right: 0;
            height: 12%; overflow: hidden;
        }
        .water-surface {
            position: absolute; inset: 0;
            background: rgba(74,124,111,.45);
        }
        .water-shimmer {
            position: absolute; top: 0; left: -100%; right: -100%; height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.4) 50%, transparent 100%);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%   { transform: translateX(-50%) scaleX(.5); opacity: 0; }
            50%  { opacity: 1; }
            100% { transform: translateX(50%) scaleX(1.5); opacity: 0; }
        }

        /* Rice plants */
        .rice-field {
            position: absolute; bottom: 28%; left: 0; right: 0;
            display: flex; justify-content: space-around; align-items: flex-end;
            padding: 0 8%;
        }
        .rice-plant {
            display: flex; flex-direction: column; align-items: center; gap: 2px;
        }
        .rice-stem {
            width: 2px; background: #4a6741;
            transform-origin: bottom center;
            animation: sway 3s ease-in-out infinite;
        }
        .rice-leaf {
            width: 14px; height: 5px;
            background: #5a7a47;
            border-radius: 50%;
            transform: rotate(-20deg);
            margin-top: -8px; margin-left: -6px;
        }
        @keyframes sway {
            0%, 100% { transform: rotate(-3deg); }
            50%       { transform: rotate(3deg); }
        }

        /* Overlay card on visual */
        .visual-card {
            position: absolute; bottom: -1.5rem; left: -1.5rem;
            background: var(--cream); border: 1px solid rgba(139,94,60,.15);
            border-radius: 12px; padding: 1rem 1.4rem;
            box-shadow: 0 8px 32px rgba(61,43,31,.15);
            min-width: 180px;
        }
        .visual-card-label { font-size: .68rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--textlt); margin-bottom: .3rem; }
        .visual-card-val { font-family: 'Fraunces', serif; font-size: 1.6rem; font-weight: 700; color: var(--water); line-height: 1; }
        .visual-card-sub { font-size: .72rem; color: var(--textlt); margin-top: .2rem; }

        .visual-card-2 {
            position: absolute; top: -1rem; right: -1rem;
            background: var(--soil); border-radius: 10px; padding: .8rem 1.2rem;
            box-shadow: 0 8px 24px rgba(61,43,31,.25);
        }
        .visual-card-2 .label { font-size: .65rem; letter-spacing: .1em; text-transform: uppercase; color: var(--straw); opacity: .7; margin-bottom: .2rem; }
        .visual-card-2 .val   { font-family: 'Fraunces', serif; font-size: 1.3rem; font-weight: 700; color: var(--straw); }

        /* Stats */
        .hero-stats {
            display: flex; gap: 2.5rem; flex-wrap: wrap; margin-top: 3rem; padding-top: 2.5rem;
            border-top: 1px solid rgba(139,94,60,.15);
            animation: fadeUp .7s .4s ease both;
        }
        .stat-item {}
        .stat-num { font-family: 'Fraunces', serif; font-size: 1.8rem; font-weight: 700; color: var(--soil); line-height: 1; }
        .stat-label { font-size: .75rem; color: var(--textlt); margin-top: .2rem; letter-spacing: .04em; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Divider ── */
        .divider-wave {
            width: 100%; overflow: hidden; line-height: 0;
            background: var(--cream);
        }
        .divider-wave svg { display: block; }

        /* ── Sections ── */
        .section-warm { background: #f5ede0; }
        .section-green { background: #eef4ea; }
        .section-cream { background: var(--cream); }

        .container { max-width: 1100px; margin: 0 auto; padding: 0 2rem; }
        .section-pad { padding: 6rem 2rem; }

        .section-label {
            font-size: .72rem; font-weight: 700; letter-spacing: .14em;
            text-transform: uppercase; color: var(--water); margin-bottom: .6rem;
        }
        .section-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 700;
            letter-spacing: -.02em; line-height: 1.15; color: var(--soil);
            margin-bottom: .9rem;
        }
        .section-sub {
            font-size: 1rem; font-weight: 300; color: var(--textlt);
            line-height: 1.75; max-width: 500px;
        }

        /* ── Feature cards ── */
        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
            gap: 1.25rem; margin-top: 3.5rem;
        }

        .feat-card {
            background: var(--cream); border: 1px solid rgba(139,94,60,.12);
            border-radius: 12px; padding: 2rem;
            transition: transform .3s, box-shadow .3s, border-color .3s;
        }
        .feat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(61,43,31,.1);
            border-color: rgba(106,171,154,.3);
        }
        .feat-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, rgba(74,124,111,.12), rgba(90,122,71,.1));
            border: 1px solid rgba(74,124,111,.2); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 1.25rem;
        }
        .feat-title { font-family: 'Fraunces', serif; font-size: 1.05rem; font-weight: 600; color: var(--soil); margin-bottom: .5rem; }
        .feat-desc { font-size: .88rem; color: var(--textlt); line-height: 1.7; font-weight: 300; }

        /* ── Formula section ── */
        .formula-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3.5rem; align-items: start; margin-top: 3.5rem; }
        @media (max-width: 768px) { .formula-grid { grid-template-columns: 1fr; } }

        .param-list { list-style: none; padding: 0; }
        .param-list li {
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .7rem 0; border-bottom: 1px solid rgba(139,94,60,.1);
            font-size: .88rem; color: var(--textlt); line-height: 1.5;
        }
        .param-list li:last-child { border-bottom: none; }
        .param-tag {
            flex-shrink: 0; background: rgba(74,124,111,.1);
            border: 1px solid rgba(74,124,111,.2); color: var(--water);
            font-family: 'Courier New', monospace; font-size: .72rem; font-weight: 700;
            padding: .18rem .55rem; border-radius: 5px;
        }

        .formula-box {
            background: var(--soil); border-radius: 12px; padding: 2rem;
            font-family: 'Courier New', monospace; font-size: .82rem;
            line-height: 2; color: var(--straw);
            box-shadow: 0 8px 32px rgba(61,43,31,.2);
        }
        .formula-box .comment { color: rgba(232,213,163,.35); }
        .formula-box .key     { color: var(--water2); font-weight: bold; }
        .formula-box .val     { color: #f0ebe0; }

        /* ── Tech stack ── */
        .tech-grid { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 2.5rem; }
        .tech-chip {
            background: var(--cream); border: 1px solid rgba(139,94,60,.18);
            border-radius: 6px; padding: .55rem 1.1rem;
            font-size: .85rem; font-weight: 500; color: var(--textlt);
            transition: border-color .2s, color .2s, transform .15s;
        }
        .tech-chip:hover { border-color: var(--water2); color: var(--water); transform: scale(1.03); }

        /* ── CTA ── */
        .cta-section {
            background: var(--soil);
            padding: 6rem 2rem; text-align: center; position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 60% 80% at 20% 50%, rgba(90,122,71,.2), transparent),
                radial-gradient(ellipse 50% 60% at 80% 40%, rgba(74,124,111,.15), transparent);
        }
        .cta-content { position: relative; z-index: 1; max-width: 580px; margin: 0 auto; }
        .cta-title { font-family: 'Fraunces', serif; font-size: clamp(2rem, 4vw, 3rem); font-weight: 700; color: var(--straw); letter-spacing: -.02em; margin-bottom: 1rem; line-height: 1.15; }
        .cta-sub { font-size: .95rem; color: rgba(232,213,163,.65); font-weight: 300; line-height: 1.7; margin-bottom: 2.5rem; }

        .btn-light {
            background: var(--straw); color: var(--soil);
            padding: .9rem 2rem; border-radius: 6px;
            font-family: 'Karla', sans-serif; font-weight: 700; font-size: .95rem;
            text-decoration: none; transition: all .2s;
            display: inline-block;
        }
        .btn-light:hover { background: var(--cream); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.2); }

        .btn-outline-light {
            border: 1.5px solid rgba(232,213,163,.35); color: var(--straw);
            padding: .9rem 2rem; border-radius: 6px;
            font-family: 'Karla', sans-serif; font-weight: 600; font-size: .95rem;
            text-decoration: none; transition: all .2s; display: inline-block;
        }
        .btn-outline-light:hover { background: rgba(232,213,163,.08); border-color: var(--straw); transform: translateY(-2px); }

        /* ── Footer ── */
        footer {
            background: #2a1e16; padding: 1.75rem 2rem; text-align: center;
            font-size: .78rem; color: rgba(232,213,163,.3);
        }
        footer a { color: rgba(106,171,154,.7); text-decoration: none; }
        footer a:hover { color: var(--water2); }

        /* ── Scroll reveal ── */
        .reveal { opacity: 0; transform: translateY(32px); transition: opacity .65s ease, transform .65s ease; }
        .reveal.visible { opacity: 1; transform: none; }
    </style>
</head>
<body>

    <!-- Nav -->
    <nav id="navbar">
        <div class="nav-logo">💧 Smart Irigasi</div>
        <div class="nav-links">
            <a href="#fitur">Fitur</a>
            <a href="#metode">Metode</a>
            <a href="{{ route('login') }}" class="nav-cta">Masuk →</a>
        </div>
    </nav>

    <!-- Hero -->
    <div style="max-width:1200px;margin:0 auto;padding:7rem 3rem 4rem;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;min-height:100vh;" class="hero-grid">
        <!-- Left -->
        <div>
            <div class="hero-eyebrow">Sistem Monitoring Irigasi Rawa</div>
            <h1 class="hero-title">
                Irigasi Lebih Cerdas,<br>
                Panen <em>Lebih Baik</em>
            </h1>
            <p class="hero-sub">
                Kalkulasi kebutuhan air otomatis dengan metode Penman-Monteith FAO-56, prediksi AI harian, dan monitoring real-time — khusus untuk lahan rawa Indonesia.
            </p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="btn-primary">Buka Dashboard →</a>
                <a href="#fitur" class="btn-outline">Lihat Fitur</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-num">FAO-56</div>
                    <div class="stat-label">Standar Internasional</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">ETo</div>
                    <div class="stat-label">Auto-Kalkulasi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">AI</div>
                    <div class="stat-label">Prediksi Harian</div>
                </div>
            </div>
        </div>

        <!-- Right — field illustration -->
        <div class="hero-visual" style="position:relative;">
            <div class="field-illustration">
                <!-- Sky & clouds -->
                <div class="cloud cloud-1"></div>
                <div class="cloud cloud-2"></div>

                <!-- Sun -->
                <div style="position:absolute;top:10%;right:15%;width:40px;height:40px;border-radius:50%;background:radial-gradient(circle,#ffe066,#ffb347);box-shadow:0 0 30px rgba(255,180,50,.6);animation:sunPulse 4s ease-in-out infinite;"></div>

                <!-- Water layer -->
                <div class="water-layer">
                    <div class="water-surface"></div>
                    <div class="water-shimmer"></div>
                </div>

                <!-- Rice plants -->
                <div class="rice-field">
                    @for($i = 0; $i < 12; $i++)
                    <div class="rice-plant" style="animation-delay: {{ $i * 0.2 }}s">
                        <div style="width:2px;height:{{ rand(28,42) }}px;background:#4a6741;transform-origin:bottom;animation:sway {{ 2.5 + ($i * 0.15) }}s ease-in-out infinite;animation-delay:{{ $i * 0.2 }}s"></div>
                        <div style="width:12px;height:4px;background:#5a7a47;border-radius:50%;transform:rotate(-20deg);margin-top:-6px;margin-left:-5px;"></div>
                    </div>
                    @endfor
                </div>

                <!-- Ground -->
                <div style="position:absolute;bottom:0;left:0;right:0;height:30%;background:linear-gradient(180deg,#a08060,#8b6843);"></div>

                <!-- Overlay gradient -->
                <div style="position:absolute;inset:0;background:linear-gradient(180deg,transparent 60%,rgba(61,43,31,.1));"></div>
            </div>

            <!-- Floating cards -->
            <div class="visual-card">
                <div class="visual-card-label">Kebutuhan Air</div>
                <div class="visual-card-val">4.2 <span style="font-size:.9rem;font-weight:300">mm</span></div>
                <div class="visual-card-sub">hari ini · normal</div>
            </div>
            <div class="visual-card-2">
                <div class="label">ETo Hari Ini</div>
                <div class="val">3.8 mm</div>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div class="divider-wave" style="background:var(--cream);">
        <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 720,0 1080,30 C1260,45 1380,20 1440,30 L1440,60 L0,60 Z" fill="#f5ede0"/>
        </svg>
    </div>

    <!-- Fitur -->
    <section id="fitur" class="section-warm section-pad">
        <div class="container">
            <div class="reveal">
                <div class="section-label">Fitur Unggulan</div>
                <h2 class="section-title">Semua yang dibutuhkan<br>untuk monitoring irigasi</h2>
                <p class="section-sub">Dari pencatatan data lapangan hingga prediksi AI — dalam satu platform yang ringan dan mudah dipakai.</p>
            </div>
            <div class="features-grid">
                <div class="feat-card reveal">
                    <div class="feat-icon">📊</div>
                    <div class="feat-title">Dashboard Real-Time</div>
                    <p class="feat-desc">Visualisasi tren kebutuhan air harian dengan grafik interaktif. Navigasi data dengan pagination AJAX tanpa reload halaman.</p>
                </div>
                <div class="feat-card reveal" style="transition-delay:.1s">
                    <div class="feat-icon">🤖</div>
                    <div class="feat-title">Prediksi AI</div>
                    <p class="feat-desc">Model Linear Regression Python memperkirakan kebutuhan air 24 jam ke depan, lengkap dengan rekomendasi tindakan otomatis.</p>
                </div>
                <div class="feat-card reveal" style="transition-delay:.2s">
                    <div class="feat-icon">⚡</div>
                    <div class="feat-title">Kalkulasi Otomatis</div>
                    <p class="feat-desc">Input data iklim → ETo, ETc, dan kebutuhan air terhitung otomatis. Ada preview real-time sebelum data disimpan.</p>
                </div>
                <div class="feat-card reveal" style="transition-delay:.3s">
                    <div class="feat-icon">🌾</div>
                    <div class="feat-title">Koefisien Tanaman</div>
                    <p class="feat-desc">Nilai Kc per fase pertumbuhan padi. ETc = ETo × Kc disesuaikan otomatis per fase awal, pertengahan, dan akhir tanam.</p>
                </div>
                <div class="feat-card reveal" style="transition-delay:.4s">
                    <div class="feat-icon">🔐</div>
                    <div class="feat-title">Autentikasi Aman</div>
                    <p class="feat-desc">Proteksi middleware Laravel memastikan hanya pengamat irigasi yang berwenang yang bisa mengakses dan mengelola data.</p>
                </div>
                <div class="feat-card reveal" style="transition-delay:.5s">
                    <div class="feat-icon">📋</div>
                    <div class="feat-title">Manajemen Data</div>
                    <p class="feat-desc">Tambah, edit, dan hapus data harian dengan mudah. Validasi otomatis dan konfirmasi sebelum penghapusan data.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div style="background:#f5ede0;">
        <svg viewBox="0 0 1440 50" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,25 C480,50 960,0 1440,25 L1440,50 L0,50 Z" fill="#eef4ea"/>
        </svg>
    </div>

    <!-- Metode -->
    <section id="metode" class="section-green section-pad">
        <div class="container">
            <div class="reveal">
                <div class="section-label">Metodologi</div>
                <h2 class="section-title">Penman-Monteith FAO-56</h2>
                <p class="section-sub">Metode standar internasional FAO untuk kalkulasi evapotranspirasi referensi — akurat dan terpercaya untuk kondisi tropis.</p>
            </div>
            <div class="formula-grid">
                <div class="reveal">
                    <p style="font-size:.9rem;color:var(--textlt);margin-bottom:1.5rem;font-weight:300">Data yang diinput petugas lapangan setiap hari:</p>
                    <ul class="param-list">
                        <li><span class="param-tag">Tmax</span> Suhu Maksimum (°C)</li>
                        <li><span class="param-tag">Tmin</span> Suhu Minimum (°C)</li>
                        <li><span class="param-tag">RH</span> Kelembaban Udara (%)</li>
                        <li><span class="param-tag">u2</span> Kecepatan Angin (m/s)</li>
                        <li><span class="param-tag">Rs</span> Radiasi Matahari (MJ/m²/hari)</li>
                        <li><span class="param-tag">Kc</span> Koefisien Tanaman</li>
                        <li><span class="param-tag">CH</span> Curah Hujan (mm)</li>
                    </ul>
                </div>
                <div class="formula-box reveal" style="transition-delay:.15s;white-space:pre;overflow-x:auto;"><span class="comment">// Kalkulasi otomatis oleh sistem</span>

<span class="key">ETo</span> = <span class="val">Penman-Monteith(</span> Tmax, Tmin, RH, u2, Rs<span class="val">)</span>

<span class="key">ETc</span> = <span class="val">ETo × Kc</span>

<span class="key">Hujan_efektif</span> = <span class="val">CH × 0.8</span>

<span class="key">Kebutuhan_Air</span> = <span class="val">max(</span> ETc - Hujan_efektif, 0 <span class="val">)</span>

<span class="comment">// Satuan output: mm/hari</span></div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div style="background:#eef4ea;">
        <svg viewBox="0 0 1440 50" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 C360,50 1080,0 1440,30 L1440,50 L0,50 Z" fill="#faf6ef"/>
        </svg>
    </div>

    <!-- Tech -->
    <section class="section-cream section-pad">
        <div class="container">
            <div class="reveal">
                <div class="section-label">Tech Stack</div>
                <h2 class="section-title">Dibangun dengan<br>teknologi yang tepat</h2>
            </div>
            <div class="tech-grid reveal" style="transition-delay:.1s">
                <span class="tech-chip">Laravel 11</span>
                <span class="tech-chip">PHP 8.2</span>
                <span class="tech-chip">Tailwind CSS</span>
                <span class="tech-chip">Blade Templating</span>
                <span class="tech-chip">Chart.js</span>
                <span class="tech-chip">Vanilla AJAX</span>
                <span class="tech-chip">Python</span>
                <span class="tech-chip">Linear Regression</span>
                <span class="tech-chip">MySQL</span>
                <span class="tech-chip">Laragon</span>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-content reveal">
            <div style="font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--water2);margin-bottom:.75rem;">Siap Digunakan</div>
            <h2 class="cta-title">Mulai monitoring<br>irigasi rawamu hari ini</h2>
            <p class="cta-sub">Dibuat oleh <strong style="color:var(--straw)">Muhammad Sauqi Khatami</strong> dari Banjarmasin — untuk mendukung pertanian cerdas di lahan rawa Indonesia.</p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <a href="{{ route('login') }}" class="btn-light">Buka Dashboard →</a>
                <a href="https://github.com/khatami99/smart-irrigation" target="_blank" class="btn-outline-light">GitHub ↗</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        Smart Irrigation System &copy; {{ date('Y') }} &nbsp;·&nbsp;
        Built by <a href="https://github.com/khatami99">khatami99</a> &nbsp;·&nbsp;
        Banjarmasin, Kalimantan Selatan
    </footer>

    <style>
        @media (max-width: 768px) {
            .hero-grid { grid-template-columns: 1fr !important; padding: 6rem 1.5rem 3rem !important; }
            .hero-visual { display: none !important; }
        }
        @keyframes sunPulse {
            0%, 100% { box-shadow: 0 0 20px rgba(255,180,50,.5); }
            50%       { box-shadow: 0 0 40px rgba(255,180,50,.8); }
        }
    </style>

    <script>
        // Scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Nav scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.style.boxShadow = '0 2px 20px rgba(61,43,31,.1)';
            } else {
                nav.style.boxShadow = 'none';
            }
        });
    </script>

</body>
</html>
