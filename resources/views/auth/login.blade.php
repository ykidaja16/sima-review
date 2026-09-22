<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SIMA-REVIEW</title>
    <meta name="description" content="Sistem Evaluasi Service Excellent — Login">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #1565c0 0%, #1976d2 40%, #1e88e5 70%, #42a5f5 100%);
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
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
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
            color: #1565c0;
            line-height: 1.2;
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
            border-color: #1976d2;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
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
            background: #1565c0;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(21, 101, 192, 0.35);
            margin-top: 4px;
        }

        .btn-login:hover {
            background: #1976d2;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(21, 101, 192, 0.4);
        }

        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.6);
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            {{-- Logo & Brand --}}
            <div class="login-brand">
                <img src="{{ asset('images/logosima.png') }}" alt="SIMA Logo">
                <div class="login-brand-text">
                    <h1>REVIEW System</h1>
                    <p>Service Excellent Evaluation</p>
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
            &copy; {{ date('Y') }} SIMA Lab — Service Excellent Evaluation System
        </div>
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
    </script>
</body>
</html>
