<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Publik | Dashboard Makro Kota Tasikmalaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            animation: page-enter .7s ease both;
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
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: #fffdf1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(244, 211, 94, .7);
            box-shadow: 0 8px 18px rgba(7, 59, 42, 0.08);
            animation: logo-enter .75s cubic-bezier(.22, 1, .36, 1) .1s both;
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
            background: #ffffff;
            border: 1px solid #e2eee6;
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(7, 59, 42, .08), 0 3px 10px rgba(7, 59, 42, .03);
            padding: 20px 24px 18px;
            animation: box-enter .65s cubic-bezier(.22, 1, .36, 1) .18s both;
        }

        .login-heading { margin-bottom: 12px; }

        .login-heading h1 {
            color: var(--primary-dark);
            font-size: 1.35rem;
            text-align: left;
            margin: 0 0 3px;
            line-height: 1.2;
            letter-spacing: -.02em;
        }

        .subtitle {
            color: var(--muted);
            margin: 0;
            font-size: .78rem;
            line-height: 1.35;
        }

        .form-group { margin-bottom: 9px; }

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
            padding: 8px 11px;
            font-size: .84rem;
            background: #fbfdfb;
            height: 36px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(31, 107, 69, .12);
            outline: none;
            background: #fff;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 9px 12px;
            font-size: .86rem;
            font-weight: 700;
            cursor: pointer;
            height: 38px;
            margin-top: 4px;
            transition: transform .18s ease, box-shadow .18s ease;
            box-shadow: 0 8px 16px rgba(7, 59, 42, .14);
        }

        .btn:hover { transform: translateY(-1px); }

        .error-box {
            background: rgba(220, 53, 69, .08);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, .18);
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 10px;
            font-size: .78rem;
            font-weight: 500;
        }

        .field-error {
            color: var(--danger);
            font-size: .74rem;
            margin-top: 2px;
            display: block;
        }

        .links {
            display: grid;
            gap: 5px;
            margin-top: 10px;
        }

        .link {
            display: inline-block;
            padding: 7px 10px;
            border: 1px solid #d4e5da;
            border-radius: 8px;
            background: #f8fbf9;
            color: var(--text);
            text-align: center;
            text-decoration: none;
            font-size: .78rem;
            font-weight: 600;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .link:hover {
            background: #edf5f0;
            border-color: #bcdbc8;
        }

        .link-secondary {
            background: transparent;
            border: none;
            color: var(--muted);
            font-size: .75rem;
            padding: 3px 6px;
        }

        .link-secondary:hover {
            color: var(--primary-dark);
            background: transparent;
        }

        .loading {
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

        .loading.is-active { opacity: 1; visibility: visible; pointer-events: auto; }
        .loading-content { display: flex; flex-direction: column; align-items: center; gap: 12px; }
        .loading-logo, .loading-logo img { width: 86px; height: 86px; object-fit: contain; }
        .loading-dots { display: inline-flex; align-items: center; gap: 5px; }
        .loading-dots span { width: 7px; height: 7px; border-radius: 50%; background: var(--primary); animation: dot-loading 1s ease-in-out infinite; }
        .loading-dots span:nth-child(2) { animation-delay: .15s; }
        .loading-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes page-enter { from { opacity: 0; } to { opacity: 1; } }
        @keyframes logo-enter { from { opacity: 0; transform: scale(.72) rotate(-8deg); } to { opacity: 1; transform: scale(1) rotate(0); } }
        @keyframes box-enter { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes dot-loading { 0%, 60%, 100% { transform: translateY(0); opacity: .35; } 30% { transform: translateY(-6px); opacity: 1; } }

        @media (prefers-reduced-motion: reduce) {
            .page, .brand-mark, .login-box, .loading, .loading-dots span { animation: none; transition: none; }
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

            .error-box {
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

            .error-box {
                text-align: center;
                font-size: 0.8rem;
            }

            .form-group {
                margin-bottom: 9px;
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

            .links {
                gap: 6px;
                margin-top: 10px;
            }

            .link {
                padding: 8px 12px;
                font-size: 0.8rem;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 9px;
                text-align: center;
            }

            .link-secondary {
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
    <div class="loading" data-register-loading aria-hidden="true">
        <div class="loading-content">
            <div class="loading-logo"><img src="{{ asset('logo.png') }}" alt=""></div>
            <div class="loading-dots" aria-label="Memuat halaman"><span></span><span></span><span></span></div>
        </div>
    </div>

    <div class="page">
        <div class="login-layout">
            <section class="brand-panel">
                <div class="brand">
                    <div class="brand-mark"><img src="{{ asset('logo.png') }}" alt="Logo Bapperida"></div>
                </div>
                <h2>Dashboard Data Indikator Pembangunan<br>Kota Tasikmalaya</h2>
                <p>Portal informasi indikator pembangunan Kota Tasikmalaya untuk pengelolaan data yang terarah dan terpercaya.</p>
            </section>

            <section class="login-box">
                <div class="login-heading">
                    <h1>Daftar Akun Publik</h1>
                    <p class="subtitle">Buat akun untuk melihat dashboard makro dan analisis indikator</p>
                </div>

                @if ($errors->any())
                    <div class="error-box">{{ $errors->first() }}</div>
                @elseif (session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('public.register.submit') }}" data-register-form>
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn" data-register-submit>Daftar Akun</button>
                </form>

                <div class="links">
                    <a href="{{ route('public.login') }}" class="link">Sudah punya akun? <strong>Masuk di sini</strong></a>
                    <a href="{{ url('/') }}" class="link link-secondary">Kembali ke Beranda</a>
                </div>
            </section>
        </div>
    </div>

    <script>
        const registerForm = document.querySelector('[data-register-form]');
        const registerLoading = document.querySelector('[data-register-loading]');
        const registerSubmit = document.querySelector('[data-register-submit]');

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            } else {
                if (registerSubmit) {
                    registerSubmit.disabled = false;
                    registerSubmit.textContent = 'Daftar Akun';
                }
                registerLoading?.classList.remove('is-active');
            }
        });

        registerForm?.addEventListener('submit', function () {
            registerLoading?.classList.add('is-active');
            registerLoading?.setAttribute('aria-hidden', 'false');
            if (registerSubmit) {
                registerSubmit.disabled = true;
                registerSubmit.textContent = 'Memuat...';
            }
        });
    </script>
</body>
</html>
