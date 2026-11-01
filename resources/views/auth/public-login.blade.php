<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Publik | Dashboard Makro Kota Tasikmalaya</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1f6b45;
            --primary-dark: #073b2a;
            --text: #16352a;
            --muted: #648074;
            --danger: #dc3545;
            --white: #ffffff;
        }

        html {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE / Edge */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
            width: 0;
            height: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                linear-gradient(rgba(255, 255, 255, .28), rgba(255, 255, 255, .28)),
                url('{{ asset('latar1.png') }}') center / cover no-repeat fixed;
            color: var(--text);
            overflow-x: hidden;
        }

        .page {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 20px;
            position: relative;
            overflow-x: hidden;
            animation: login-page-enter .7s ease both;
        }

        .page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: #ffffff;
        }

        .login-layout {
            width: min(100%, 1060px);
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(330px, 400px);
            align-items: center;
            gap: 48px;
            position: relative;
            z-index: 1;
        }

        .brand-panel {
            max-width: 520px;
            transform: translateY(-8px);
        }

        .brand-mark {
            animation: login-logo-enter .75s cubic-bezier(.22, 1, .36, 1) .1s both;
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: #fffdf1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(244, 211, 94, .7);
            box-shadow: 0 8px 18px rgba(7, 59, 42, 0.08);
        }

        .brand-mark img { width: 50px; height: 50px; object-fit: contain; }

        .brand {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .brand-panel h2 {
            margin: 0 0 8px;
            color: var(--primary-dark);
            max-width: 600px;
            font-size: clamp(1.75rem, 3vw, 2.35rem);
            line-height: 1.12;
            letter-spacing: -.03em;
        }

        .brand-panel p {
            max-width: 400px;
            margin: 0;
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.55;
        }

        .login-box {
            width: 100%;
            max-width: none;
            background: #ffffff;
            border: 1px solid #e2eee6;
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(7, 59, 42, .08), 0 3px 10px rgba(7, 59, 42, .03);
            padding: 22px 26px 20px;
            animation: login-box-enter .65s cubic-bezier(.22, 1, .36, 1) .18s both;
        }

        .login-heading { margin-bottom: 14px; }

        .login-heading h1 {
            color: var(--primary-dark);
            font-size: 1.35rem;
            text-align: left;
            margin: 0 0 3px;
            line-height: 1.2;
            letter-spacing: -.02em;
        }

        .subtitle {
            text-align: left;
            color: var(--muted);
            margin: 0;
            font-size: 0.8rem;
            line-height: 1.35;
        }

        .form-group { margin-bottom: 11px; }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 3px;
            font-size: 0.78rem;
            color: var(--text);
        }

        input {
            width: 100%;
            border: 1px solid #d4e5da;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            background: #fbfdfb;
            height: 38px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(31, 107, 69, 0.12);
            outline: none;
            background: #fff;
        }

        .input-password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-password-wrapper input {
            padding-right: 40px !important;
        }

        .toggle-pass-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: 1.05rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 6px;
            transition: color .15s ease, background-color .15s ease;
            z-index: 2;
        }

        .toggle-pass-btn:hover {
            color: var(--primary-dark);
            background-color: rgba(31, 107, 69, .08);
        }

        .toggle-pass-btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 9px 14px;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            height: 40px;
            margin-top: 4px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            box-shadow: 0 8px 16px rgba(7, 59, 42, 0.14);
        }

        .btn:hover { transform: translateY(-1px); }

        .error-box {
            background: rgba(220, 53, 69, 0.08);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.18);
            border-radius: 8px;
            padding: 8px 11px;
            margin-bottom: 11px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-box {
            background: #f8fbf9;
            color: var(--primary-dark);
            border: 1px solid #d4e5da;
            border-radius: 8px;
            padding: 8px 11px;
            margin-bottom: 11px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .field-error {
            color: var(--danger);
            font-size: 0.74rem;
            margin-top: 2px;
            display: block;
        }

        .login-links {
            margin-top: 10px;
            display: grid;
            gap: 5px;
        }

        .login-link {
            display: inline-block;
            text-align: center;
            padding: 7px 11px;
            border-radius: 8px;
            border: 1px solid #d4e5da;
            background: #f8fbf9;
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            font-size: .78rem;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .login-link:hover {
            background: #edf5f0;
            border-color: #bcdbc8;
        }

        .back-link {
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: .75rem;
            padding: 3px 6px;
        }

        .back-link:hover {
            color: var(--primary-dark);
            background: transparent;
        }

        .portal-switch {
            margin: 4px 0;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            text-align: center;
        }

        .portal-link {
            display: inline-block;
            text-decoration: none;
            color: #475569;
            font-size: .78rem;
            font-weight: 600;
            transition: color .15s ease;
        }

        .portal-link:hover {
            color: #0f172a;
        }

        .login-loading {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: grid;
            place-items: center;
            background: rgba(248, 250, 252, .96);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .28s ease, visibility .28s ease;
        }

        .login-loading.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .loading-logo {
            display: grid;
            place-items: center;
            width: 86px;
            height: 86px;
        }

        .loading-logo img { width: 86px; height: 86px; object-fit: contain; }

        .loading-dots { display: inline-flex; align-items: center; gap: 5px; }

        .loading-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            animation: dot-loading 1s ease-in-out infinite;
        }

        .loading-dots span:nth-child(2) { animation-delay: .15s; }
        .loading-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes login-page-enter {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes login-logo-enter {
            from { opacity: 0; transform: scale(.72) rotate(-8deg); }
            to { opacity: 1; transform: scale(1) rotate(0); }
        }

        @keyframes login-box-enter {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes dot-loading {
            0%, 60%, 100% { transform: translateY(0); opacity: .35; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .page, .brand-mark, .login-box, .login-loading, .loading-dots span { animation: none; transition: none; }
        }

        @media (max-width: 900px) {
            .page {
                padding: 24px 16px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-layout {
                grid-template-columns: 1fr;
                gap: 20px;
                max-width: 440px;
                margin: 0 auto;
                width: 100%;
            }

            .brand-panel {
                max-width: none;
                text-align: center;
                transform: none;
                margin: 0 auto;
                width: 100%;
            }

            .brand {
                justify-content: center;
                margin-bottom: 12px;
            }

            .brand-panel h2 {
                text-align: center;
                font-size: clamp(1.35rem, 3.8vw, 1.75rem);
                margin-bottom: 6px;
                line-height: 1.25;
            }

            .brand-panel p {
                text-align: center;
                margin: 0 auto;
                font-size: .86rem;
                max-width: 400px;
            }

            .login-box {
                margin: 0 auto;
                width: 100%;
            }

            .login-heading {
                text-align: center;
            }

            .login-heading h1 {
                text-align: center;
            }

            .subtitle {
                text-align: center;
            }

            .error-box, .status-box {
                text-align: center;
            }
        }

        @media (max-width: 600px) {
            body {
                background-attachment: scroll;
            }

            .page {
                padding: 24px 16px;
                min-height: 100vh;
                min-height: 100dvh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-layout {
                gap: 18px;
                max-width: 100%;
                width: 100%;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .brand-panel {
                transform: none;
                padding: 0 4px;
                text-align: center;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .brand {
                margin-bottom: 10px;
                justify-content: center;
            }

            .brand-mark {
                width: 56px;
                height: 56px;
                border-radius: 14px;
                box-shadow: 0 4px 12px rgba(7, 59, 42, 0.08);
            }

            .brand-mark img {
                width: 40px;
                height: 40px;
            }

            .brand-panel h2 {
                font-size: clamp(1.15rem, 4.8vw, 1.4rem);
                line-height: 1.28;
                letter-spacing: -.02em;
                margin-bottom: 6px;
                text-align: center;
            }

            .brand-panel p {
                font-size: 0.8rem;
                line-height: 1.45;
                max-width: 330px;
                margin: 0 auto;
                text-align: center;
            }

            .login-box {
                padding: 20px 20px 18px;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(7, 59, 42, .08);
                width: 100%;
                max-width: 390px;
                margin: 0 auto;
            }

            .login-heading {
                margin-bottom: 12px;
                text-align: center;
            }

            .login-heading h1 {
                font-size: 1.3rem;
                text-align: center;
                margin-bottom: 4px;
            }

            .subtitle {
                font-size: 0.78rem;
                margin-bottom: 12px;
                line-height: 1.4;
                text-align: center;
            }

            .error-box, .status-box {
                text-align: center;
                font-size: 0.8rem;
            }

            .form-group {
                margin-bottom: 11px;
                text-align: left;
            }

            label {
                font-size: 0.78rem;
                margin-bottom: 3px;
                display: block;
            }

            input {
                font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
                padding: 9px 12px;
                border-radius: 9px;
                height: 40px;
            }

            .btn {
                padding: 10px 14px;
                font-size: 0.9rem;
                height: 42px;
                border-radius: 10px;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-links {
                gap: 6px;
                margin-top: 10px;
            }

            .login-link {
                padding: 8px 12px;
                font-size: 0.8rem;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 9px;
                text-align: center;
            }

            .back-link {
                height: auto;
                font-size: 0.76rem;
                padding: 4px 6px;
            }
        }

        @media (max-width: 380px) {
            .page {
                padding: 16px 12px;
            }

            .login-box {
                padding: 16px 14px 14px;
            }

            .brand-panel h2 {
                font-size: 1.1rem;
            }

            .login-heading h1 {
                font-size: 1.15rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-loading" data-login-loading aria-hidden="true">
        <div class="loading-content">
            <div class="loading-logo">
                <img src="{{ asset('logo.png') }}" alt="">
            </div>
            <div class="loading-dots" aria-label="Memuat halaman">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="login-layout">
            <section class="brand-panel">
                <div class="brand">
                    <div class="brand-mark">
                        <img src="{{ asset('logo.png') }}" alt="Logo Bapperida">
                    </div>
                </div>
                <h2>Dashboard Data Indikator Pembangunan<br>Kota Tasikmalaya</h2>
                <p>Portal informasi indikator pembangunan Kota Tasikmalaya untuk pengelolaan data yang terarah dan terpercaya.</p>
            </section>

            <section class="login-box">
                <div class="login-heading">
                    <h1>Login Pengguna</h1>
                    <div class="subtitle">Masuk untuk melihat dashboard makro dan analisis indikator</div>
                </div>

                @if ($errors->any())
                    <div class="error-box">
                        {{ $errors->first() }}
                    </div>
                @elseif (session('error'))
                    <div class="error-box">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="status-box">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('public.login.submit') }}" autocomplete="off" data-login-form>
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="off" required @if ($errors->any()) autofocus @else readonly data-autofill-guard @endif>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-password-wrapper">
                            <input id="password" type="password" name="password" autocomplete="new-password" required @if (!$errors->any()) readonly data-autofill-guard @endif>
                            <button type="button" class="toggle-pass-btn" data-toggle-target="password" title="Lihat password" aria-label="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn" data-login-submit>Masuk</button>
                </form>

                <div class="login-links">
                    <a href="{{ route('public.register') }}" class="login-link" style="background: #fff8ec; border-color: #f4d35e; color: var(--primary-dark);">Belum punya akun? <strong>Daftar Akun Baru</strong></a>
                    <a href="{{ url('/') }}" class="login-link back-link">Kembali ke Beranda</a>
                </div>
            </section>
        </div>
    </div>

    <script>
        const loginForm = document.querySelector('[data-login-form]');
        const loginLoading = document.querySelector('[data-login-loading]');
        const loginSubmit = document.querySelector('[data-login-submit]');

        document.querySelectorAll('[data-autofill-guard]').forEach(function (field) {
            field.addEventListener('focus', function () {
                field.removeAttribute('readonly');
            }, { once: true });
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            } else {
                if (loginSubmit) {
                    loginSubmit.disabled = false;
                    loginSubmit.textContent = 'Masuk';
                }
                loginLoading?.classList.remove('is-active');
            }
        });

        loginForm?.addEventListener('submit', function () {
            loginLoading?.classList.add('is-active');
            loginLoading?.setAttribute('aria-hidden', 'false');
            if (loginSubmit) {
                loginSubmit.disabled = true;
                loginSubmit.textContent = 'Memuat...';
            }
        });

        document.querySelectorAll('[data-toggle-target]').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-toggle-target');
                const input = document.getElementById(targetId);
                if (!input) return;

                if (input.hasAttribute('readonly')) {
                    input.removeAttribute('readonly');
                }

                const icon = button.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.className = 'bi bi-eye-slash';
                    }
                    button.title = 'Sembunyikan password';
                    button.setAttribute('aria-label', 'Sembunyikan password');
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.className = 'bi bi-eye';
                    }
                    button.title = 'Lihat password';
                    button.setAttribute('aria-label', 'Lihat password');
                }
            });
        });
    </script>
</body>
</html>
