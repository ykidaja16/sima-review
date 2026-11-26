<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SIMA-MONEV</title>
    <meta name="description" content="Sistem Evaluasi Service Excellence — Login">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @php
        $loginThemes = [
            'sima_emerald'   => ['name' => 'SIMA Signature',  'bg' => 'linear-gradient(160deg, #0b3c5d 0%, #0d5c63 50%, #166d3b 100%)', 'swatch' => 'linear-gradient(135deg, #0284c7, #16a34a)', 'btn' => 'linear-gradient(135deg, #0d5c63, #166d3b)', 'primary' => '#0d5c63'],
            'royal_indigo'   => ['name' => 'Royal Indigo',    'bg' => 'linear-gradient(160deg, #1e3a8a 0%, #312e81 52%, #4338ca 100%)', 'swatch' => 'linear-gradient(135deg, #2563eb, #6366f1)', 'btn' => 'linear-gradient(135deg, #2563eb, #4f46e5)', 'primary' => '#3730a3'],
            'ocean_breeze'   => ['name' => 'Ocean Breeze',    'bg' => 'linear-gradient(160deg, #0c4a6e 0%, #0369a1 52%, #0891b2 100%)', 'swatch' => 'linear-gradient(135deg, #0284c7, #06b6d4)', 'btn' => 'linear-gradient(135deg, #0284c7, #0891b2)', 'primary' => '#0369a1'],
            'aurora_violet'  => ['name' => 'Aurora Violet',   'bg' => 'linear-gradient(160deg, #4c1d95 0%, #6d28d9 52%, #be185d 100%)', 'swatch' => 'linear-gradient(135deg, #6366f1, #ec4899)', 'btn' => 'linear-gradient(135deg, #7c3aed, #db2777)', 'primary' => '#6d28d9'],
            'emerald_mint'   => ['name' => 'Emerald Mint',    'bg' => 'linear-gradient(160deg, #064e3b 0%, #047857 52%, #0d9488 100%)', 'swatch' => 'linear-gradient(135deg, #059669, #14b8a6)', 'btn' => 'linear-gradient(135deg, #059669, #0d9488)', 'primary' => '#047857'],
            'coral_rose'     => ['name' => 'Coral Rose',      'bg' => 'linear-gradient(160deg, #881337 0%, #be123c 52%, #e11d48 100%)', 'swatch' => 'linear-gradient(135deg, #f43f5e, #fb7185)', 'btn' => 'linear-gradient(135deg, #be123c, #e11d48)', 'primary' => '#be123c'],
            'amber_sunset'   => ['name' => 'Amber Sunset',    'bg' => 'linear-gradient(160deg, #7c2d12 0%, #b45309 52%, #c2410c 100%)', 'swatch' => 'linear-gradient(135deg, #f97316, #ef4444)', 'btn' => 'linear-gradient(135deg, #d97706, #ea580c)', 'primary' => '#b45309'],
            'berry_night'    => ['name' => 'Berry Night',     'bg' => 'linear-gradient(160deg, #3b0764 0%, #701a75 52%, #9d174d 100%)', 'swatch' => 'linear-gradient(135deg, #581c87, #be185d)', 'btn' => 'linear-gradient(135deg, #7e22ce, #be185d)', 'primary' => '#701a75'],
            'deep_navy'      => ['name' => 'Deep Navy',       'bg' => 'linear-gradient(160deg, #0a192f 0%, #172554 55%, #1e40af 100%)', 'swatch' => 'linear-gradient(135deg, #1e3a8a, #1d4ed8)', 'btn' => 'linear-gradient(135deg, #1e3a8a, #2563eb)', 'primary' => '#1e3a8a'],
            'periwinkle_sky' => ['name' => 'Periwinkle Sky',  'bg' => 'linear-gradient(160deg, #1e3a8a 0%, #2563eb 52%, #4f46e5 100%)', 'swatch' => 'linear-gradient(135deg, #60a5fa, #818cf8)', 'btn' => 'linear-gradient(135deg, #2563eb, #6366f1)', 'primary' => '#2563eb'],
            'teal_forest'    => ['name' => 'Teal Forest',     'bg' => 'linear-gradient(160deg, #134e4a 0%, #115e59 52%, #0f766e 100%)', 'swatch' => 'linear-gradient(135deg, #0f766e, #10b981)', 'btn' => 'linear-gradient(135deg, #0f766e, #059669)', 'primary' => '#0f766e'],
            'sage_pine'      => ['name' => 'Sage Pine',       'bg' => 'linear-gradient(160deg, #1e352f 0%, #2d4f44 55%, #3d6b58 100%)', 'swatch' => 'linear-gradient(135deg, #3f6255, #6b9080)', 'btn' => 'linear-gradient(135deg, #2d4f44, #52796f)', 'primary' => '#2d4f44'],
            'olive_moss'     => ['name' => 'Olive Moss',      'bg' => 'linear-gradient(160deg, #283618 0%, #3a5a40 55%, #4f6f52 100%)', 'swatch' => 'linear-gradient(135deg, #588157, #a3b18a)', 'btn' => 'linear-gradient(135deg, #3a5a40, #588157)', 'primary' => '#3a5a40'],
            'crimson_noir'   => ['name' => 'Crimson Noir',    'bg' => 'linear-gradient(160deg, #1c0d14 0%, #31101d 55%, #4a1525 100%)', 'swatch' => 'linear-gradient(135deg, #1f1218, #581c2c)', 'btn' => 'linear-gradient(135deg, #881337, #be123c)', 'primary' => '#881337'],
            'slate_midnight' => ['name' => 'Slate Midnight',  'bg' => 'linear-gradient(160deg, #0f172a 0%, #1e1b4b 55%, #1e293b 100%)', 'swatch' => 'linear-gradient(135deg, #0f172a, #312e81)', 'btn' => 'linear-gradient(135deg, #1e293b, #312e81)', 'primary' => '#1e293b'],
        ];
    @endphp
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --login-bg: linear-gradient(160deg, #0b3c5d 0%, #0d5c63 50%, #166d3b 100%);
            --login-btn: linear-gradient(135deg, #0d5c63, #166d3b);
            --login-primary: #0d5c63;
            --login-swatch: linear-gradient(135deg, #0284c7, #16a34a);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--login-bg);
            transition: background 0.3s ease;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 36px 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.22);
        }

        /* Logo area */
        .login-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .login-brand img {
            height: 52px;
        }

        .login-brand-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--login-primary);
            line-height: 1.2;
            transition: color 0.25s ease;
        }

        .login-brand-text p {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #90a4ae;
            margin-top: 2px;
        }

        .login-heading {
            margin-bottom: 24px;
        }

        .login-heading h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .login-heading p {
            font-size: 0.875rem;
            color: #90a4ae;
        }

        /* Alert */
        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.83rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-danger  { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }

        /* Form */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 0.73rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 8px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #b0bec5;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: #f8fafc;
            border: 1.5px solid #e8edf2;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #1a202c;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-control::placeholder { color: #b0bec5; }

        .form-control:focus {
            border-color: var(--login-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.76rem;
            margin-top: 5px;
        }

        .show-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #b0bec5;
            font-size: 1rem;
            transition: color 0.2s;
            padding: 2px;
        }
        .show-pass:hover { color: #64748b; }

        /* Btn */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--login-btn);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
            margin-top: 4px;
        }

        .btn-login:hover {
            opacity: 0.94;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
        }

        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.72);
        }

        /* Theme Picker Widget */
        .theme-picker-floating {
            position: fixed;
            bottom: 22px;
            right: 24px;
            z-index: 1080;
        }

        .theme-picker-fab {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid #ffffff;
            background: var(--login-swatch);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .theme-picker-fab:hover {
            transform: scale(1.07);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        @keyframes themeFadeUp {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .theme-picker-popover {
            display: none;
            position: absolute;
            bottom: calc(100% + 12px);
            right: 0;
            width: 282px;
            background: #ffffff;
            border-radius: 18px;
            padding: 18px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.24), 0 4px 12px rgba(15, 23, 42, 0.08);
            animation: themeFadeUp 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .theme-picker-popover.show {
            display: block;
        }

        .theme-picker-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        .theme-picker-subtitle {
            font-size: 0.73rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        .theme-swatch-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 14px 0;
        }

        .theme-swatch-btn {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1rem;
            position: relative;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);
        }

        .theme-swatch-btn:hover {
            transform: translateY(-2px);
        }

        .theme-swatch-btn.active {
            box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #1e293b;
        }

        .theme-picker-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 4px;
            font-size: 0.75rem;
        }

        .theme-active-name {
            font-weight: 700;
            color: #1e293b;
        }

        .theme-esc-hint {
            color: #94a3b8;
            background: none;
            border: none;
            font-size: 0.73rem;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
        }

        .theme-esc-hint:hover {
            color: #475569;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <style id="loginThemeDynamicStyle"></style>
    <script id="simaLoginThemesData" type="application/json">{!! json_encode($loginThemes) !!}</script>
    <script>
        (function() {
            let themes = {};
            try {
                themes = JSON.parse(document.getElementById('simaLoginThemesData').textContent || '{}');
            } catch (e) {}
            const savedKey = localStorage.getItem('sima_theme_pref') || 'sima_emerald';
            const active = themes[savedKey] || themes['sima_emerald'];
            const styleEl = document.getElementById('loginThemeDynamicStyle');
            if (styleEl && active) {
                styleEl.textContent = `:root { --login-bg: ${active.bg}; --login-btn: ${active.btn}; --login-primary: ${active.primary}; --login-swatch: ${active.swatch}; }`;
            }
            window.__simaLoginThemes = themes;
            window.__simaInitialThemeKey = themes[savedKey] ? savedKey : 'sima_emerald';
        })();
    </script>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            {{-- Logo & Brand --}}
            <div class="login-brand">
                <img src="{{ asset('images/logosima.png') }}" alt="SIMA Logo">
                <div class="login-brand-text">
                    <h1>MONEV System</h1>
                    <p>Service Excellence Evaluation</p>
                </div>
            </div>

            {{-- Heading --}}
            <div class="login-heading">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <input type="hidden" name="selected_login_theme" id="selectedLoginThemeInput" value="">

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-group">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            autofocus
                        >
                    </div>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            style="padding-right:40px;"
                        >
                        <button type="button" class="show-pass" id="togglePass">
                            <i class="bi bi-eye-fill" id="passIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} SIMA Lab — Service Excellence Evaluation System
        </div>
    </div>

    <!-- Floating Pemilih Tema Warna -->
    <div class="theme-picker-floating">
        <div class="theme-picker-popover" id="themePickerPopover" role="dialog" aria-label="Pilih Tema Warna">
            <div class="theme-picker-title">Tema warna</div>
            <div class="theme-picker-subtitle">Tersimpan otomatis saat Anda masuk</div>
            <div class="theme-swatch-grid">
                @foreach($loginThemes as $tKey => $tItem)
                <button type="button"
                    class="theme-swatch-btn"
                    style="background: {{ $tItem['swatch'] }};"
                    data-key="{{ $tKey }}"
                    data-name="{{ $tItem['name'] }}"
                    title="{{ $tItem['name'] }}">
                </button>
                @endforeach
            </div>
            <div class="theme-picker-footer">
                <span class="theme-active-name" id="themeActiveName">SIMA Signature</span>
                <button type="button" class="theme-esc-hint" id="themeEscHint">Esc untuk tutup</button>
            </div>
        </div>
        <button type="button" class="theme-picker-fab" id="themePickerFab" title="Ubah Tema Warna">
            <i class="bi bi-droplet"></i>
        </button>
    </div>

    <script>
        // Submit state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation:spin 0.7s linear infinite;display:inline-block;"></i> Memproses...';
            btn.disabled = true;
        });

        // Toggle password
        document.getElementById('togglePass').addEventListener('click', function() {
            const p = document.getElementById('password');
            const ico = document.getElementById('passIcon');
            if (p.type === 'password') {
                p.type = 'text';
                ico.className = 'bi bi-eye-slash-fill';
            } else {
                p.type = 'password';
                ico.className = 'bi bi-eye-fill';
            }
        });

        // Pemilih Tema Warna Halaman Login
        (function() {
            const themes = window.__simaLoginThemes || {};
            let activeKey = window.__simaInitialThemeKey || 'sima_emerald';
            const fab = document.getElementById('themePickerFab');
            const popover = document.getElementById('themePickerPopover');
            const escHint = document.getElementById('themeEscHint');
            const nameLabel = document.getElementById('themeActiveName');
            const styleEl = document.getElementById('loginThemeDynamicStyle');
            const hiddenInput = document.getElementById('selectedLoginThemeInput');
            const swatchBtns = document.querySelectorAll('.theme-swatch-btn');

            function syncUI(key) {
                const t = themes[key] || themes['sima_emerald'];
                if (!t) return;
                if (styleEl) {
                    styleEl.textContent = `:root { --login-bg: ${t.bg}; --login-btn: ${t.btn}; --login-primary: ${t.primary}; --login-swatch: ${t.swatch}; }`;
                }
                if (nameLabel) nameLabel.textContent = t.name;
                swatchBtns.forEach(b => {
                    if (b.dataset.key === key) {
                        b.classList.add('active');
                        b.innerHTML = '<i class="bi bi-check-lg"></i>';
                    } else {
                        b.classList.remove('active');
                        b.innerHTML = '';
                    }
                });
            }

            syncUI(activeKey);

            if (fab && popover) {
                fab.addEventListener('click', function(e) {
                    e.stopPropagation();
                    popover.classList.toggle('show');
                });

                if (escHint) {
                    escHint.addEventListener('click', function() {
                        popover.classList.remove('show');
                    });
                }

                document.addEventListener('click', function(e) {
                    if (!popover.contains(e.target) && !fab.contains(e.target)) {
                        popover.classList.remove('show');
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && popover.classList.contains('show')) {
                        popover.classList.remove('show');
                    }
                });

                swatchBtns.forEach(btn => {
                    btn.addEventListener('mouseenter', function() {
                        if (nameLabel) nameLabel.textContent = btn.dataset.name;
                    });
                    btn.addEventListener('mouseleave', function() {
                        if (nameLabel && themes[activeKey]) nameLabel.textContent = themes[activeKey].name;
                    });
                    btn.addEventListener('click', function() {
                        activeKey = btn.dataset.key;
                        localStorage.setItem('sima_theme_pref', activeKey);
                        if (hiddenInput) hiddenInput.value = activeKey;
                        syncUI(activeKey);
                    });
                });
            }
        })();
    </script>
</body>
</html>
