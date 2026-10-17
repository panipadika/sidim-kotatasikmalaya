<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Makro Kota Tasikmalaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1f6b45;
            --primary-dark: #073b2a;
            --primary-soft: #e9f5ee;
            --gold: #f4d35e;
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

        .brand-mark {
            animation: login-logo-enter .75s cubic-bezier(.22, 1, .36, 1) .1s both;
        }

        .login-box {
            animation: login-box-enter .65s cubic-bezier(.22, 1, .36, 1) .18s both;
        }

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

        @media (prefers-reduced-motion: reduce) {
            .page, .brand-mark, .login-box { animation: none; }
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
        }

        .login-heading {
            margin-bottom: 14px;
        }

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
            margin-bottom: 14px;
            font-size: 0.8rem;
            line-height: 1.35;
        }

        .form-group {
            margin-bottom: 11px;
        }

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

        .btn:hover {
            transform: translateY(-1px);
        }

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

        .field-error {
            color: var(--danger);
            font-size: 0.74rem;
            margin-top: 2px;
            display: block;
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

        .loading-logo img {
            width: 86px;
            height: 86px;
            object-fit: contain;
        }

        .loading-dots {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .loading-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            animation: dot-loading 1s ease-in-out infinite;
        }

        .loading-dots span:nth-child(2) { animation-delay: .15s; }
        .loading-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes dot-loading {
            0%, 60%, 100% { transform: translateY(0); opacity: .35; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-loading, .loading-dots span { transition: none; animation: none; }
        }

        @media (max-width: 900px) {
            .page {
                padding: 20px 16px;
            }

            .login-layout {
                grid-template-columns: 1fr;
                gap: 16px;
                max-width: 420px;
                margin: 0 auto;
            }

            .brand-panel {
                max-width: none;
                text-align: center;
                transform: none;
                margin: 0 auto;
            }

            .brand {
                justify-content: center;
                margin-bottom: 10px;
            }

            .brand-panel h2 {
                text-align: center;
                font-size: clamp(1.25rem, 3.5vw, 1.65rem);
                margin-bottom: 4px;
                line-height: 1.2;
            }

            .brand-panel p {
                text-align: center;
                margin: 0 auto;
                font-size: .84rem;
                max-width: 380px;
            }
        }

        @media (max-width: 600px) {
            body {
                background-attachment: scroll;
            }

            .page {
                padding: 16px 14px 20px;
                min-height: 100vh;
                min-height: 100dvh;
                align-items: flex-start;
            }

            .login-layout {
                gap: 12px;
                max-width: 100%;
            }

            .brand-panel {
                transform: none;
                padding: 0 4px;
            }

            .brand {
                margin-bottom: 8px;
            }

            .brand-mark {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(7, 59, 42, 0.08);
            }

            .brand-mark img {
                width: 36px;
                height: 36px;
            }

            .brand-panel h2 {
                font-size: clamp(1.1rem, 4.5vw, 1.3rem);
                line-height: 1.25;
                letter-spacing: -.02em;
                margin-bottom: 3px;
            }

            .brand-panel h2 br {
                display: none;
            }

            .brand-panel p {
                font-size: 0.76rem;
                line-height: 1.38;
                max-width: 320px;
            }

            .login-box {
                padding: 18px 16px 16px;
                border-radius: 14px;
                box-shadow: 0 8px 24px rgba(7, 59, 42, .07);
            }

            .login-heading {
                margin-bottom: 10px;
            }

            .login-heading h1 {
                font-size: 1.2rem;
            }

            .subtitle {
                font-size: 0.76rem;
                margin-bottom: 10px;
                line-height: 1.35;
            }

            .form-group {
                margin-bottom: 9px;
            }

            label {
                font-size: 0.76rem;
                margin-bottom: 2px;
            }

            input {
                font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
                padding: 8px 11px;
                border-radius: 8px;
                height: 38px;
            }

            .btn {
                padding: 9px 12px;
                font-size: 0.88rem;
                height: 38px;
                border-radius: 8px;
            }
        }

        @media (max-width: 380px) {
            .page {
                padding: 12px 10px 18px;
            }

            .login-box {
                padding: 14px 12px 12px;
            }

            .brand-panel h2 {
                font-size: 1.05rem;
            }

            .login-heading h1 {
                font-size: 1.12rem;
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
                <h2>Dashboard Data Indikator<br>Pembangunan Kota Tasikmalaya</h2>
                <p>Portal informasi indikator pembangunan Kota Tasikmalaya untuk pengelolaan data yang terarah dan terpercaya.</p>
            </section>

            <section class="login-box">
                <div class="login-heading">
                    <h1>Login Administrator</h1>
                    <div class="subtitle">Masuk untuk mengelola data dashboard</div>
                </div>

                @if ($errors->any())
                    <div class="error-box">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" autocomplete="off" data-login-form>
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" autocomplete="off" required @if ($errors->any()) autofocus @else readonly data-autofill-guard @endif>
                        @error('username')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" required @if (!$errors->any()) readonly data-autofill-guard @endif>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn" data-login-submit>Masuk Admin</button>
                </form>
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

        loginForm?.addEventListener('submit', function () {
            loginLoading?.classList.add('is-active');
            loginLoading?.setAttribute('aria-hidden', 'false');
            if (loginSubmit) {
                loginSubmit.disabled = true;
                loginSubmit.textContent = 'Memuat...';
            }
        });
    </script>
</body>
</html>
