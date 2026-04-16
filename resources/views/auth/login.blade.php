<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Smart Irrigation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,600;0,700;1,300;1,400&family=Karla:wght@300;400;500;600&display=swap" rel="stylesheet">
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
            --text:   #4a3728;
            --textlt: #7a6355;
            --border: rgba(139,94,60,.18);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Karla', sans-serif;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--cream2);
            color: var(--text);
        }
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .login-visual { display: none; }
        }

        /* Left panel — visual */
        .login-visual {
            background: var(--soil);
            display: flex; flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .login-visual::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 70% 60% at 20% 30%, rgba(74,124,111,.25), transparent),
                radial-gradient(ellipse 50% 70% at 80% 80%, rgba(90,122,71,.2), transparent);
        }
        .visual-content { position: relative; z-index: 1; }
        .visual-logo {
            font-family: 'Fraunces', serif;
            font-size: 1.2rem; font-weight: 700; color: var(--straw);
        }
        .visual-logo span { color: var(--water2); font-style: italic; font-weight: 300; }
        .visual-tagline {
            font-family: 'Fraunces', serif;
            font-size: 2.2rem; font-weight: 700; color: var(--straw);
            line-height: 1.2; letter-spacing: -.02em;
            margin-top: 3rem;
        }
        .visual-tagline em { color: var(--water2); font-style: italic; font-weight: 300; }
        .visual-sub {
            font-size: .9rem; color: rgba(232,213,163,.55);
            font-weight: 300; line-height: 1.7; margin-top: 1rem; max-width: 300px;
        }

        /* Illustrated field */
        .field-mini {
            position: relative; height: 160px;
            border-radius: 12px; overflow: hidden;
            margin-top: 2rem;
            background: linear-gradient(180deg, #a8d8ea 0%, #7db87a 40%, #6b7a3d 65%, #b8956a 100%);
        }
        .sun-mini {
            position: absolute; top: 12%; right: 15%;
            width: 28px; height: 28px; border-radius: 50%;
            background: radial-gradient(circle, #ffe066, #ffb347);
            box-shadow: 0 0 20px rgba(255,180,50,.6);
        }
        .water-mini {
            position: absolute; bottom: 32%; left: 0; right: 0; height: 10%;
            background: rgba(74,124,111,.4);
        }
        .ground-mini {
            position: absolute; bottom: 0; left: 0; right: 0; height: 32%;
            background: linear-gradient(180deg, #a08060, #8b6843);
        }

        /* Stats */
        .visual-stats {
            display: flex; gap: 2rem; position: relative; z-index: 1;
        }
        .vs-item {}
        .vs-num { font-family: 'Fraunces', serif; font-size: 1.5rem; font-weight: 700; color: var(--water2); }
        .vs-label { font-size: .7rem; color: rgba(232,213,163,.4); letter-spacing: .06em; text-transform: uppercase; margin-top: .15rem; }

        /* Right panel — form */
        .login-form-panel {
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 2rem;
        }
        .login-form-wrap { width: 100%; max-width: 380px; }
        .form-title {
            font-family: 'Fraunces', serif;
            font-size: 2rem; font-weight: 700; color: var(--soil);
            letter-spacing: -.02em; margin-bottom: .4rem;
        }
        .form-sub { font-size: .9rem; color: var(--textlt); font-weight: 300; margin-bottom: 2.5rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .8rem; font-weight: 600; color: var(--text); margin-bottom: .5rem; letter-spacing: .01em; }
        .form-input {
            width: 100%;
            background: var(--cream); border: 1.5px solid rgba(139,94,60,.18);
            color: var(--text); border-radius: 8px;
            padding: .8rem 1rem; font-size: .9rem; font-family: 'Karla', sans-serif;
            transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .form-input:focus {
            border-color: var(--water);
            box-shadow: 0 0 0 3px rgba(74,124,111,.12);
        }
        .form-input::placeholder { color: rgba(122,99,85,.4); }
        .form-input.error { border-color: #b94a3c; }

        .form-check { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.75rem; }
        .form-check input { width: 15px; height: 15px; accent-color: var(--water); cursor: pointer; }
        .form-check label { font-size: .85rem; color: var(--textlt); cursor: pointer; }

        .btn-login {
            width: 100%; background: var(--soil); color: var(--straw);
            padding: .9rem; border-radius: 8px; border: none;
            font-family: 'Karla', sans-serif; font-size: .95rem; font-weight: 600;
            cursor: pointer; transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 4px 16px rgba(61,43,31,.2);
        }
        .btn-login:hover { background: var(--soil2); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(61,43,31,.3); }
        .btn-login:active { transform: translateY(0); }

        .alert-error {
            background: rgba(185,74,60,.08); border: 1px solid rgba(185,74,60,.22);
            color: #a03828; border-radius: 8px; padding: .8rem 1rem;
            font-size: .85rem; margin-bottom: 1.25rem;
        }
        .alert-success {
            background: rgba(74,124,111,.08); border: 1px solid rgba(74,124,111,.22);
            color: var(--water); border-radius: 8px; padding: .8rem 1rem;
            font-size: .85rem; margin-bottom: 1.25rem;
        }

        .footer-copy { font-size: .72rem; color: rgba(122,99,85,.4); text-align: center; margin-top: 2rem; }

        /* Loading */
        #loading-bar { position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(90deg, var(--water), var(--water2)); z-index: 9999; transition: all .3s ease-out; }
        #page-overlay { position: fixed; inset: 0; background: var(--cream); z-index: 998; display: flex; align-items: center; justify-content: center; transition: opacity .3s; }
    </style>
</head>
<body>

    <div id="loading-bar" style="width:0%;opacity:0;"></div>
    <div id="page-overlay" style="opacity:0;pointer-events:none;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;">
            <div style="width:32px;height:32px;border:3px solid var(--water2);border-top-color:transparent;border-radius:50%;animation:spin .8s linear infinite;"></div>
            <p style="font-size:.85rem;color:var(--textlt);">Memuat...</p>
        </div>
    </div>
    <style>@keyframes spin { to { transform:rotate(360deg); } }</style>

    {{-- Left Visual --}}
    <div class="login-visual">
        <div class="visual-content">
            <a href="{{ url('/') }}" class="visual-logo">💧 Smart <span> Irigasi </span></a>
            <div class="visual-tagline">
                Kelola irigasi<br>lebih <em>cerdas</em>
            </div>
            <p class="visual-sub">Monitoring kebutuhan air lahan rawa berbasis AI & metode FAO-56.</p>

            <div class="field-mini">
                <div class="sun-mini"></div>
                <div class="water-mini"></div>
                <div class="ground-mini"></div>
            </div>
        </div>
        <div class="visual-stats">
            <div class="vs-item">
                <div class="vs-num">FAO-56</div>
                <div class="vs-label">Standar Kalkulasi</div>
            </div>
            <div class="vs-item">
                <div class="vs-num">AI</div>
                <div class="vs-label">Prediksi Harian</div>
            </div>
            <div class="vs-item">
                <div class="vs-num">ETo</div>
                <div class="vs-label">Auto-Hitung</div>
            </div>
        </div>
    </div>

    {{-- Right Form --}}
    <div class="login-form-panel">
        <div class="login-form-wrap">
            <h1 class="form-title">Selamat datang</h1>
            <p class="form-sub">Masuk untuk mengakses panel monitoring irigasi.</p>

            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif
            @if(session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                        type="email" id="email" name="email"
                        value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <input class="form-input" type="password" id="password"
                        name="password" required placeholder="••••••••">
                </div>

                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn-login">Masuk →</button>
            </form>

            <p class="footer-copy">Smart Irrigation System &copy; {{ date('Y') }} · Palangkaraya</p>
        </div>
    </div>

    <script>
        const overlay = document.getElementById('page-overlay');
        const bar = document.getElementById('loading-bar');
        function showOverlay() {
            overlay.style.opacity='1'; overlay.style.pointerEvents='all';
            bar.style.opacity='1'; bar.style.width='70%';
        }
        function hideOverlay() {
            overlay.style.opacity='0'; overlay.style.pointerEvents='none';
            bar.style.width='100%';
            setTimeout(() => { bar.style.opacity='0'; setTimeout(() => bar.style.width='0%', 300); }, 200);
        }
        window.addEventListener('load', hideOverlay);
        document.addEventListener('submit', showOverlay);
    </script>

</body>
</html>
